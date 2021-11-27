# frozen_string_literal: true

module Jekyll
  class CollectionReader
    SPECIAL_COLLECTIONS = %w(posts data).freeze

    attr_reader :site, :content
    def initialize(site)
      @site = site
      @content = {}
    end

    # Read in all collections specified in the configuration
    #
    # Returns nothing.
    def read
      site.collections.each_value do |collection|
        collection.read unless SPECIAL_COLLECTIONS.include?(collection.label)
      end
    end
  end
end
