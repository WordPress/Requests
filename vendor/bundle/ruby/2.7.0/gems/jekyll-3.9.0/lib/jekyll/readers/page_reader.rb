# frozen_string_literal: true

module Jekyll
  class PageReader
    attr_reader :site, :dir, :unfiltered_content
    def initialize(site, dir)
      @site = site
      @dir = dir
      @unfiltered_content = []
    end

    # Read all the files in <source>/<dir>/ for Yaml header and create a new Page
    # object for each file.
    #
    # dir - The String relative path of the directory to read.
    #
    # Returns an array of static pages.
    def read(files)
      files.map do |page|
        @unfiltered_content << Page.new(@site, @site.source, @dir, page)
      end
      @unfiltered_content.select { |page| site.publisher.publish?(page) }
    end
  end
end
