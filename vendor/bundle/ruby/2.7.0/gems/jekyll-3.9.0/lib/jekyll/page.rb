# frozen_string_literal: true

module Jekyll
  class Page
    include Convertible

    attr_writer :dir
    attr_accessor :site, :pager
    attr_accessor :name, :ext, :basename
    attr_accessor :data, :content, :output

    alias_method :extname, :ext

    FORWARD_SLASH = "/".freeze

    # Attributes for Liquid templates
    ATTRIBUTES_FOR_LIQUID = %w(
      content
      dir
      name
      path
      url
    ).freeze

    # A set of extensions that are considered HTML or HTML-like so we
    # should not alter them,  this includes .xhtml through XHTM5.

    HTML_EXTENSIONS = %w(
      .html
      .xhtml
      .htm
    ).freeze

    # Initialize a new Page.
    #
    # site - The Site object.
    # base - The String path to the source.
    # dir  - The String path between the source and the file.
    # name - The String filename of the file.
    def initialize(site, base, dir, name)
      @site = site
      @base = base
      @dir  = dir
      @name = name
      @path = if site.in_theme_dir(base) == base # we're in a theme
                site.in_theme_dir(base, dir, name)
              else
                site.in_source_dir(base, dir, name)
              end

      process(name)
      read_yaml(File.join(base, dir), name)

      data.default_proc = proc do |_, key|
        site.frontmatter_defaults.find(File.join(dir, name), type, key)
      end

      Jekyll::Hooks.trigger :pages, :post_init, self
    end

    # The generated directory into which the page will be placed
    # upon generation. This is derived from the permalink or, if
    # permalink is absent, will be '/'
    #
    # Returns the String destination directory.
    def dir
      if url.end_with?(FORWARD_SLASH)
        url
      else
        url_dir = File.dirname(url)
        url_dir.end_with?(FORWARD_SLASH) ? url_dir : "#{url_dir}/"
      end
    end

    # The full path and filename of the post. Defined in the YAML of the post
    # body.
    #
    # Returns the String permalink or nil if none has been set.
    def permalink
      data.nil? ? nil : data["permalink"]
    end

    # The template of the permalink.
    #
    # Returns the template String.
    def template
      if !html?
        "/:path/:basename:output_ext"
      elsif index?
        "/:path/"
      else
        Utils.add_permalink_suffix("/:path/:basename", site.permalink_style)
      end
    end

    # The generated relative url of this page. e.g. /about.html.
    #
    # Returns the String url.
    def url
      @url ||= URL.new({
        :template     => template,
        :placeholders => url_placeholders,
        :permalink    => permalink,
      }).to_s
    end

    # Returns a hash of URL placeholder names (as symbols) mapping to the
    # desired placeholder replacements. For details see "url.rb"
    def url_placeholders
      {
        :path       => @dir,
        :basename   => basename,
        :output_ext => output_ext,
      }
    end

    # Extract information from the page filename.
    #
    # name - The String filename of the page file.
    #
    # Returns nothing.
    def process(name)
      self.ext = File.extname(name)
      self.basename = name[0..-ext.length - 1]
    end

    # Add any necessary layouts to this post
    #
    # layouts      - The Hash of {"name" => "layout"}.
    # site_payload - The site payload Hash.
    #
    # Returns String rendered page.
    def render(layouts, site_payload)
      site_payload["page"] = to_liquid
      site_payload["paginator"] = pager.to_liquid

      do_layout(site_payload, layouts)
    end

    # The path to the source file
    #
    # Returns the path to the source file
    def path
      data.fetch("path") { relative_path }
    end

    # The path to the page source file, relative to the site source
    def relative_path
      File.join(*[@dir, @name].map(&:to_s).reject(&:empty?)).sub(%r!\A\/!, "")
    end

    # Obtain destination path.
    #
    # dest - The String path to the destination dir.
    #
    # Returns the destination file path String.
    def destination(dest)
      path = site.in_dest_dir(dest, URL.unescape_path(url))
      path = File.join(path, "index") if url.end_with?("/")
      path << output_ext unless path.end_with? output_ext
      path
    end

    # Returns the object as a debug String.
    def inspect
      "#<Jekyll::Page @name=#{name.inspect}>"
    end

    # Returns the Boolean of whether this Page is HTML or not.
    def html?
      HTML_EXTENSIONS.include?(output_ext)
    end

    # Returns the Boolean of whether this Page is an index file or not.
    def index?
      basename == "index"
    end

    def trigger_hooks(hook_name, *args)
      Jekyll::Hooks.trigger :pages, hook_name, self, *args
    end

    def write?
      true
    end
  end
end
