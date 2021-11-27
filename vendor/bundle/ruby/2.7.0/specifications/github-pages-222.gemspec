# -*- encoding: utf-8 -*-
# stub: github-pages 222 ruby lib

Gem::Specification.new do |s|
  s.name = "github-pages".freeze
  s.version = "222"

  s.required_rubygems_version = Gem::Requirement.new(">= 0".freeze) if s.respond_to? :required_rubygems_version=
  s.require_paths = ["lib".freeze]
  s.authors = ["GitHub, Inc.".freeze]
  s.date = "2021-11-16"
  s.description = "Bootstrap the GitHub Pages Jekyll environment locally.".freeze
  s.email = "support@github.com".freeze
  s.executables = ["github-pages".freeze]
  s.files = ["bin/github-pages".freeze]
  s.homepage = "https://github.com/github/pages-gem".freeze
  s.licenses = ["MIT".freeze]
  s.required_ruby_version = Gem::Requirement.new(">= 2.3.0".freeze)
  s.rubygems_version = "3.1.6".freeze
  s.summary = "Track GitHub Pages dependencies.".freeze

  s.installed_by_version = "3.1.6" if s.respond_to? :installed_by_version

  if s.respond_to? :specification_version then
    s.specification_version = 4
  end

  if s.respond_to? :add_runtime_dependency then
    s.add_runtime_dependency(%q<jekyll>.freeze, ["= 3.9.0"])
    s.add_runtime_dependency(%q<jekyll-sass-converter>.freeze, ["= 1.5.2"])
    s.add_runtime_dependency(%q<kramdown>.freeze, ["= 2.3.1"])
    s.add_runtime_dependency(%q<kramdown-parser-gfm>.freeze, ["= 1.1.0"])
    s.add_runtime_dependency(%q<jekyll-commonmark-ghpages>.freeze, ["= 0.1.6"])
    s.add_runtime_dependency(%q<liquid>.freeze, ["= 4.0.3"])
    s.add_runtime_dependency(%q<rouge>.freeze, ["= 3.26.0"])
    s.add_runtime_dependency(%q<github-pages-health-check>.freeze, ["= 1.17.9"])
    s.add_runtime_dependency(%q<jekyll-redirect-from>.freeze, ["= 0.16.0"])
    s.add_runtime_dependency(%q<jekyll-sitemap>.freeze, ["= 1.4.0"])
    s.add_runtime_dependency(%q<jekyll-feed>.freeze, ["= 0.15.1"])
    s.add_runtime_dependency(%q<jekyll-gist>.freeze, ["= 1.5.0"])
    s.add_runtime_dependency(%q<jekyll-paginate>.freeze, ["= 1.1.0"])
    s.add_runtime_dependency(%q<jekyll-coffeescript>.freeze, ["= 1.1.1"])
    s.add_runtime_dependency(%q<jekyll-seo-tag>.freeze, ["= 2.7.1"])
    s.add_runtime_dependency(%q<jekyll-github-metadata>.freeze, ["= 2.13.0"])
    s.add_runtime_dependency(%q<jekyll-avatar>.freeze, ["= 0.7.0"])
    s.add_runtime_dependency(%q<jekyll-remote-theme>.freeze, ["= 0.4.3"])
    s.add_runtime_dependency(%q<jemoji>.freeze, ["= 0.12.0"])
    s.add_runtime_dependency(%q<jekyll-mentions>.freeze, ["= 1.6.0"])
    s.add_runtime_dependency(%q<jekyll-relative-links>.freeze, ["= 0.6.1"])
    s.add_runtime_dependency(%q<jekyll-optional-front-matter>.freeze, ["= 0.3.2"])
    s.add_runtime_dependency(%q<jekyll-readme-index>.freeze, ["= 0.3.0"])
    s.add_runtime_dependency(%q<jekyll-default-layout>.freeze, ["= 0.1.4"])
    s.add_runtime_dependency(%q<jekyll-titles-from-headings>.freeze, ["= 0.5.3"])
    s.add_runtime_dependency(%q<minima>.freeze, ["= 2.5.1"])
    s.add_runtime_dependency(%q<jekyll-swiss>.freeze, ["= 1.0.0"])
    s.add_runtime_dependency(%q<jekyll-theme-primer>.freeze, ["= 0.6.0"])
    s.add_runtime_dependency(%q<jekyll-theme-architect>.freeze, ["= 0.2.0"])
    s.add_runtime_dependency(%q<jekyll-theme-cayman>.freeze, ["= 0.2.0"])
    s.add_runtime_dependency(%q<jekyll-theme-dinky>.freeze, ["= 0.2.0"])
    s.add_runtime_dependency(%q<jekyll-theme-hacker>.freeze, ["= 0.2.0"])
    s.add_runtime_dependency(%q<jekyll-theme-leap-day>.freeze, ["= 0.2.0"])
    s.add_runtime_dependency(%q<jekyll-theme-merlot>.freeze, ["= 0.2.0"])
    s.add_runtime_dependency(%q<jekyll-theme-midnight>.freeze, ["= 0.2.0"])
    s.add_runtime_dependency(%q<jekyll-theme-minimal>.freeze, ["= 0.2.0"])
    s.add_runtime_dependency(%q<jekyll-theme-modernist>.freeze, ["= 0.2.0"])
    s.add_runtime_dependency(%q<jekyll-theme-slate>.freeze, ["= 0.2.0"])
    s.add_runtime_dependency(%q<jekyll-theme-tactile>.freeze, ["= 0.2.0"])
    s.add_runtime_dependency(%q<jekyll-theme-time-machine>.freeze, ["= 0.2.0"])
    s.add_runtime_dependency(%q<mercenary>.freeze, ["~> 0.3"])
    s.add_runtime_dependency(%q<nokogiri>.freeze, [">= 1.12.5", "< 2.0"])
    s.add_runtime_dependency(%q<terminal-table>.freeze, ["~> 1.4"])
    s.add_development_dependency(%q<jekyll_test_plugin_malicious>.freeze, ["~> 0.2"])
    s.add_development_dependency(%q<pry>.freeze, ["~> 0.10"])
    s.add_development_dependency(%q<rspec>.freeze, ["~> 3.3"])
    s.add_development_dependency(%q<rubocop-github>.freeze, ["= 0.16.0"])
  else
    s.add_dependency(%q<jekyll>.freeze, ["= 3.9.0"])
    s.add_dependency(%q<jekyll-sass-converter>.freeze, ["= 1.5.2"])
    s.add_dependency(%q<kramdown>.freeze, ["= 2.3.1"])
    s.add_dependency(%q<kramdown-parser-gfm>.freeze, ["= 1.1.0"])
    s.add_dependency(%q<jekyll-commonmark-ghpages>.freeze, ["= 0.1.6"])
    s.add_dependency(%q<liquid>.freeze, ["= 4.0.3"])
    s.add_dependency(%q<rouge>.freeze, ["= 3.26.0"])
    s.add_dependency(%q<github-pages-health-check>.freeze, ["= 1.17.9"])
    s.add_dependency(%q<jekyll-redirect-from>.freeze, ["= 0.16.0"])
    s.add_dependency(%q<jekyll-sitemap>.freeze, ["= 1.4.0"])
    s.add_dependency(%q<jekyll-feed>.freeze, ["= 0.15.1"])
    s.add_dependency(%q<jekyll-gist>.freeze, ["= 1.5.0"])
    s.add_dependency(%q<jekyll-paginate>.freeze, ["= 1.1.0"])
    s.add_dependency(%q<jekyll-coffeescript>.freeze, ["= 1.1.1"])
    s.add_dependency(%q<jekyll-seo-tag>.freeze, ["= 2.7.1"])
    s.add_dependency(%q<jekyll-github-metadata>.freeze, ["= 2.13.0"])
    s.add_dependency(%q<jekyll-avatar>.freeze, ["= 0.7.0"])
    s.add_dependency(%q<jekyll-remote-theme>.freeze, ["= 0.4.3"])
    s.add_dependency(%q<jemoji>.freeze, ["= 0.12.0"])
    s.add_dependency(%q<jekyll-mentions>.freeze, ["= 1.6.0"])
    s.add_dependency(%q<jekyll-relative-links>.freeze, ["= 0.6.1"])
    s.add_dependency(%q<jekyll-optional-front-matter>.freeze, ["= 0.3.2"])
    s.add_dependency(%q<jekyll-readme-index>.freeze, ["= 0.3.0"])
    s.add_dependency(%q<jekyll-default-layout>.freeze, ["= 0.1.4"])
    s.add_dependency(%q<jekyll-titles-from-headings>.freeze, ["= 0.5.3"])
    s.add_dependency(%q<minima>.freeze, ["= 2.5.1"])
    s.add_dependency(%q<jekyll-swiss>.freeze, ["= 1.0.0"])
    s.add_dependency(%q<jekyll-theme-primer>.freeze, ["= 0.6.0"])
    s.add_dependency(%q<jekyll-theme-architect>.freeze, ["= 0.2.0"])
    s.add_dependency(%q<jekyll-theme-cayman>.freeze, ["= 0.2.0"])
    s.add_dependency(%q<jekyll-theme-dinky>.freeze, ["= 0.2.0"])
    s.add_dependency(%q<jekyll-theme-hacker>.freeze, ["= 0.2.0"])
    s.add_dependency(%q<jekyll-theme-leap-day>.freeze, ["= 0.2.0"])
    s.add_dependency(%q<jekyll-theme-merlot>.freeze, ["= 0.2.0"])
    s.add_dependency(%q<jekyll-theme-midnight>.freeze, ["= 0.2.0"])
    s.add_dependency(%q<jekyll-theme-minimal>.freeze, ["= 0.2.0"])
    s.add_dependency(%q<jekyll-theme-modernist>.freeze, ["= 0.2.0"])
    s.add_dependency(%q<jekyll-theme-slate>.freeze, ["= 0.2.0"])
    s.add_dependency(%q<jekyll-theme-tactile>.freeze, ["= 0.2.0"])
    s.add_dependency(%q<jekyll-theme-time-machine>.freeze, ["= 0.2.0"])
    s.add_dependency(%q<mercenary>.freeze, ["~> 0.3"])
    s.add_dependency(%q<nokogiri>.freeze, [">= 1.12.5", "< 2.0"])
    s.add_dependency(%q<terminal-table>.freeze, ["~> 1.4"])
    s.add_dependency(%q<jekyll_test_plugin_malicious>.freeze, ["~> 0.2"])
    s.add_dependency(%q<pry>.freeze, ["~> 0.10"])
    s.add_dependency(%q<rspec>.freeze, ["~> 3.3"])
    s.add_dependency(%q<rubocop-github>.freeze, ["= 0.16.0"])
  end
end
