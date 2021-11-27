# frozen_string_literal: true

module Jekyll
  module Filters
    module URLFilters
      # Produces an absolute URL based on site.url and site.baseurl.
      #
      # input - the URL to make absolute.
      #
      # Returns the absolute URL as a String.
      def absolute_url(input)
        return if input.nil?
        input = input.url if input.respond_to?(:url)
        return input if Addressable::URI.parse(input.to_s).absolute?
        site = @context.registers[:site]
        return relative_url(input) if site.config["url"].nil?
        Addressable::URI.parse(
          site.config["url"].to_s + relative_url(input)
        ).normalize.to_s
      end

      # Produces a URL relative to the domain root based on site.baseurl
      # unless it is already an absolute url with an authority (host).
      #
      # input - the URL to make relative to the domain root
      #
      # Returns a URL relative to the domain root as a String.
      def relative_url(input)
        return if input.nil?
        input = input.url if input.respond_to?(:url)
        return input if Addressable::URI.parse(input.to_s).absolute?

        parts = [sanitized_baseurl, input]
        Addressable::URI.parse(
          parts.compact.map { |part| ensure_leading_slash(part.to_s) }.join
        ).normalize.to_s
      end

      # Strips trailing `/index.html` from URLs to create pretty permalinks
      #
      # input - the URL with a possible `/index.html`
      #
      # Returns a URL with the trailing `/index.html` removed
      def strip_index(input)
        return if input.nil? || input.to_s.empty?
        input.sub(%r!/index\.html?$!, "/")
      end

      private

      def sanitized_baseurl
        site = @context.registers[:site]
        site.config["baseurl"].to_s.chomp("/")
      end

      def ensure_leading_slash(input)
        return input if input.nil? || input.empty? || input.start_with?("/")
        "/#{input}"
      end

    end
  end
end
