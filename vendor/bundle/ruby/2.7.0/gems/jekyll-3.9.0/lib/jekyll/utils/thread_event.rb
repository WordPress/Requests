# frozen_string_literal: true

require "thread"

module Jekyll
  module Utils
    # Based on the pattern and code from
    # https://emptysqua.re/blog/an-event-synchronization-primitive-for-ruby/
    class ThreadEvent
      attr_reader :flag

      def initialize
        @lock = Mutex.new
        @cond = ConditionVariable.new
        @flag = false
      end

      def set
        @lock.synchronize do
          yield if block_given?
          @flag = true
          @cond.broadcast
        end
      end

      def wait
        @lock.synchronize do
          unless @flag
            @cond.wait(@lock)
          end
        end
      end
    end
  end
end
