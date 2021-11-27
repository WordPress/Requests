# frozen_string_literal: true

require "rubocop"

module RuboCop
  module Cop
    module Jekyll
      class NoPAllowed < Cop
        MSG = "Avoid using `p` to print things. Use `Jekyll.logger` instead.".freeze

        def_node_search :p_called?, <<-PATTERN
        (send _ :p _)
        PATTERN

        def on_send(node)
          if p_called?(node)
            add_offense(node, :location => :selector)
          end
        end
      end
    end
  end
end
