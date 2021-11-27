# -*- encoding: utf-8 -*-
# stub: rb-fsevent 0.11.0 ruby lib

Gem::Specification.new do |s|
  s.name = "rb-fsevent".freeze
  s.version = "0.11.0"

  s.required_rubygems_version = Gem::Requirement.new(">= 0".freeze) if s.respond_to? :required_rubygems_version=
  s.metadata = { "source_code_uri" => "https://github.com/thibaudgg/rb-fsevent" } if s.respond_to? :metadata=
  s.require_paths = ["lib".freeze]
  s.authors = ["Thibaud Guillaume-Gentil".freeze, "Travis Tilley".freeze]
  s.date = "2021-05-08"
  s.description = "FSEvents API with Signals catching (without RubyCocoa)".freeze
  s.email = ["thibaud@thibaud.gg".freeze, "ttilley@gmail.com".freeze]
  s.homepage = "http://rubygems.org/gems/rb-fsevent".freeze
  s.licenses = ["MIT".freeze]
  s.rubygems_version = "3.1.6".freeze
  s.summary = "Very simple & usable FSEvents API".freeze

  s.installed_by_version = "3.1.6" if s.respond_to? :installed_by_version

  if s.respond_to? :specification_version then
    s.specification_version = 4
  end

  if s.respond_to? :add_runtime_dependency then
    s.add_development_dependency(%q<rspec>.freeze, ["~> 3.6"])
    s.add_development_dependency(%q<guard-rspec>.freeze, ["~> 4.2"])
    s.add_development_dependency(%q<rake>.freeze, ["~> 12.0"])
  else
    s.add_dependency(%q<rspec>.freeze, ["~> 3.6"])
    s.add_dependency(%q<guard-rspec>.freeze, ["~> 4.2"])
    s.add_dependency(%q<rake>.freeze, ["~> 12.0"])
  end
end
