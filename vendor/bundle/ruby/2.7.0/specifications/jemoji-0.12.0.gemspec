# -*- encoding: utf-8 -*-
# stub: jemoji 0.12.0 ruby lib

Gem::Specification.new do |s|
  s.name = "jemoji".freeze
  s.version = "0.12.0"

  s.required_rubygems_version = Gem::Requirement.new(">= 0".freeze) if s.respond_to? :required_rubygems_version=
  s.require_paths = ["lib".freeze]
  s.authors = ["GitHub, Inc.".freeze]
  s.date = "2020-03-05"
  s.email = "support@github.com".freeze
  s.homepage = "https://github.com/jekyll/jemoji".freeze
  s.licenses = ["MIT".freeze]
  s.required_ruby_version = Gem::Requirement.new(">= 2.4.0".freeze)
  s.rubygems_version = "3.1.6".freeze
  s.summary = "GitHub-flavored emoji plugin for Jekyll".freeze

  s.installed_by_version = "3.1.6" if s.respond_to? :installed_by_version

  if s.respond_to? :specification_version then
    s.specification_version = 4
  end

  if s.respond_to? :add_runtime_dependency then
    s.add_runtime_dependency(%q<gemoji>.freeze, ["~> 3.0"])
    s.add_runtime_dependency(%q<html-pipeline>.freeze, ["~> 2.2"])
    s.add_runtime_dependency(%q<jekyll>.freeze, [">= 3.0", "< 5.0"])
    s.add_development_dependency(%q<bundler>.freeze, [">= 0"])
    s.add_development_dependency(%q<rake>.freeze, ["~> 12.0"])
    s.add_development_dependency(%q<rspec>.freeze, ["~> 3.0"])
    s.add_development_dependency(%q<rubocop-jekyll>.freeze, ["~> 0.4"])
  else
    s.add_dependency(%q<gemoji>.freeze, ["~> 3.0"])
    s.add_dependency(%q<html-pipeline>.freeze, ["~> 2.2"])
    s.add_dependency(%q<jekyll>.freeze, [">= 3.0", "< 5.0"])
    s.add_dependency(%q<bundler>.freeze, [">= 0"])
    s.add_dependency(%q<rake>.freeze, ["~> 12.0"])
    s.add_dependency(%q<rspec>.freeze, ["~> 3.0"])
    s.add_dependency(%q<rubocop-jekyll>.freeze, ["~> 0.4"])
  end
end
