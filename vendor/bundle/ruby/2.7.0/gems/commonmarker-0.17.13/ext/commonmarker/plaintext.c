#include "node.h"
#include "syntax_extension.h"
#include "render.h"

#define OUT(s, wrap, escaping) renderer->out(renderer, node, s, wrap, escaping)
#define LIT(s) renderer->out(renderer, node, s, false, LITERAL)
#define CR() renderer->cr(renderer)
#define BLANKLINE() renderer->blankline(renderer)
#define LISTMARKER_SIZE 20

// Functions to convert cmark_nodes to plain text strings.

static CMARK_INLINE void outc(cmark_renderer *renderer, cmark_node *node, 
                              cmark_escaping escape,
                              int32_t c, unsigned char nextc) {
  cmark_render_code_point(renderer, c);
}

// if node is a block node, returns node.
// otherwise returns first block-level node that is an ancestor of node.
// if there is no block-level ancestor, returns NULL.
static cmark_node *get_containing_block(cmark_node *node) {
  while (node) {
    if (CMARK_NODE_BLOCK_P(node)) {
      return node;
    } else {
      node = node->parent;
    }
  }
  return NULL;
}

static int S_render_node(cmark_renderer *renderer, cmark_node *node,
                         cmark_event_type ev_type, int options) {
  cmark_node *tmp;
  int list_number;
  cmark_delim_type list_delim;
  int i;
  bool entering = (ev_type == CMARK_EVENT_ENTER);
  char listmarker[LISTMARKER_SIZE];
  bool first_in_list_item;
  bufsize_t marker_width;
  bool allow_wrap = renderer->width > 0 && !(CMARK_OPT_NOBREAKS & options) &&
                    !(CMARK_OPT_HARDBREAKS & options);

  // Don't adjust tight list status til we've started the list.
  // Otherwise we loose the blank line between a paragraph and
  // a following list.
  if (!(node->type == CMARK_NODE_ITEM && node->prev == NULL && entering)) {
    tmp = get_containing_block(node);
    renderer->in_tight_list_item =
        tmp && // tmp might be NULL if there is no containing block
        ((tmp->type == CMARK_NODE_ITEM &&
          cmark_node_get_list_tight(tmp->parent)) ||
         (tmp && tmp->parent && tmp->parent->type == CMARK_NODE_ITEM &&
          cmark_node_get_list_tight(tmp->parent->parent)));
  }

  if (node->extension && node->extension->plaintext_render_func) {
    node->extension->plaintext_render_func(node->extension, renderer, node, ev_type, options);
    return 1;
  }

  switch (node->type) {
  case CMARK_NODE_DOCUMENT:
    break;

  case CMARK_NODE_BLOCK_QUOTE:
    break;

  case CMARK_NODE_LIST:
    if (!entering && node->next && (node->next->type == CMARK_NODE_CODE_BLOCK ||
                                    node->next->type == CMARK_NODE_LIST)) {
      CR();
    }
    break;

  case CMARK_NODE_ITEM:
    if (cmark_node_get_list_type(node->parent) == CMARK_BULLET_LIST) {
      marker_width = 4;
    } else {
      list_number = cmark_node_get_list_start(node->parent);
      list_delim = cmark_node_get_list_delim(node->parent);
      tmp = node;
      while (tmp->prev) {
        tmp = tmp->prev;
        list_number += 1;
      }
      // we ensure a width of at least 4 so
      // we get nice transition from single digits
      // to double
      snprintf(listmarker, LISTMARKER_SIZE, "%d%s%s", list_number,
               list_delim == CMARK_PAREN_DELIM ? ")" : ".",
               list_number < 10 ? "  " : " ");
      marker_width = (bufsize_t)strlen(listmarker);
    }
    if (entering) {
      if (cmark_node_get_list_type(node->parent) == CMARK_BULLET_LIST) {
        LIT("  - ");
        renderer->begin_content = true;
      } else {
        LIT(listmarker);
        renderer->begin_content = true;
      }
      for (i = marker_width; i--;) {
        cmark_strbuf_putc(renderer->prefix, ' ');
      }
    } else {
      cmark_strbuf_truncate(renderer->prefix,
                            renderer->prefix->size - marker_width);
      CR();
    }
    break;

  case CMARK_NODE_HEADING:
    if (entering) {
      renderer->begin_content = true;
      renderer->no_linebreaks = true;
    } else {
      renderer->no_linebreaks = false;
      BLANKLINE();
    }
    break;

  case CMARK_NODE_CODE_BLOCK:
    first_in_list_item = node->prev == NULL && node->parent &&
                         node->parent->type == CMARK_NODE_ITEM;

    if (!first_in_list_item) {
      BLANKLINE();
    }
    OUT(cmark_node_get_literal(node), false, LITERAL);
    BLANKLINE();
    break;

  case CMARK_NODE_HTML_BLOCK:
    break;

  case CMARK_NODE_CUSTOM_BLOCK:
    break;

  case CMARK_NODE_THEMATIC_BREAK:
    BLANKLINE();
    break;

  case CMARK_NODE_PARAGRAPH:
    if (!entering) {
      BLANKLINE();
    }
    break;

  case CMARK_NODE_TEXT:
    OUT(cmark_node_get_literal(node), allow_wrap, NORMAL);
    break;

  case CMARK_NODE_LINEBREAK:
    CR();
    break;

  case CMARK_NODE_SOFTBREAK:
    if (CMARK_OPT_HARDBREAKS & options) {
      CR();
    } else if (!renderer->no_linebreaks && renderer->width == 0 &&
               !(CMARK_OPT_HARDBREAKS & options) &&
               !(CMARK_OPT_NOBREAKS & options)) {
      CR();
    } else {
      OUT(" ", allow_wrap, LITERAL);
    }
    break;

  case CMARK_NODE_CODE:
    OUT(cmark_node_get_literal(node), allow_wrap, LITERAL);
    break;

  case CMARK_NODE_HTML_INLINE:
    break;

  case CMARK_NODE_CUSTOM_INLINE:
    break;

  case CMARK_NODE_STRONG:
    break;

  case CMARK_NODE_EMPH:
    break;

  case CMARK_NODE_LINK:
    break;

  case CMARK_NODE_IMAGE:
    break;

  default:
    assert(false);
    break;
  }

  return 1;
}

char *cmark_render_plaintext(cmark_node *root, int options, int width) {
  return cmark_render_plaintext_with_mem(root, options, width, cmark_node_mem(root));
}

char *cmark_render_plaintext_with_mem(cmark_node *root, int options, int width, cmark_mem *mem) {
  if (options & CMARK_OPT_HARDBREAKS) {
    // disable breaking on width, since it has
    // a different meaning with OPT_HARDBREAKS
    width = 0;
  }
  return cmark_render(mem, root, options, width, outc, S_render_node);
}
