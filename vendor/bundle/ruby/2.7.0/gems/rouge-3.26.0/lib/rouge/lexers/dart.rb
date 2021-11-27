# -*- coding: utf-8 -*- #
# frozen_string_literal: true

module Rouge
  module Lexers
    class Dart < RegexLexer
      title "Dart"
      desc "The Dart programming language (dartlang.com)"

      tag 'dart'
      filenames '*.dart'
      mimetypes 'text/x-dart'

      keywords = %w(
        as assert break case catch continue default do else finally for
        if in is new rethrow return super switch this throw try while with
      )

      declarations = %w(
        abstract dynamic const external extends factory final get implements
        native operator set static typedef var
      )

      types = %w(bool double Dynamic enum int num Object Set String void)

      imports = %w(import export library part\s*of part source)

      id = /[a-zA-Z_]\w*/

      state :root do
        rule %r(^
          (\s*(?:[a-zA-Z_][a-zA-Z\d_.\[\]]*\s+)+?) # return arguments
          ([a-zA-Z_]\w*)                           # method name
          (\s*)(\()                                # signature start
        )mx do |m|
          # TODO: do this better, this shouldn't need a delegation
          delegate Dart, m[1]
          token Name::Function, m[2]
          token Text, m[3]
          token Punctuation, m[4]
        end

        rule %r/\s+/, Text
        rule %r(//.*?$), Comment::Single
        rule %r(/\*.*?\*/)m, Comment::Multiline
        rule %r/"/, Str, :dqs
        rule %r/'/, Str, :sqs
        rule %r/r"[^"]*"/, Str::Other
        rule %r/r'[^']*'/, Str::Other
        rule %r/##{id}*/i, Str::Symbol
        rule %r/@#{id}/, Name::Decorator
        rule %r/(?:#{keywords.join('|')})\b/, Keyword
        rule %r/(?:#{declarations.join('|')})\b/, Keyword::Declaration
        rule %r/(?:#{types.join('|')})\b/, Keyword::Type
        rule %r/(?:true|false|null)\b/, Keyword::Constant
        rule %r/(?:class|interface)\b/, Keyword::Declaration, :class
        rule %r/(?:#{imports.join('|')})\b/, Keyword::Namespace, :import
        rule %r/(\.)(#{id})/ do
          groups Operator, Name::Attribute
        end

        rule %r/#{id}:/, Name::Label
        rule %r/\$?#{id}/, Name
        rule %r/[~^*!%&\[\](){}<>\|+=:;,.\/?-]/, Operator
        rule %r/\d*\.\d+([eE]\-?\d+)?/, Num::Float
        rule %r/0x[\da-fA-F]+/, Num::Hex
        rule %r/\d+L?/, Num::Integer
        rule %r/\n/, Text
      end

      state :class do
        rule %r/\s+/m, Text
        rule id, Name::Class, :pop!
      end

      state :dqs do
        rule %r/"/, Str, :pop!
        rule %r/[^\\\$"]+/, Str
        mixin :string
      end

      state :sqs do
        rule %r/'/, Str, :pop!
        rule %r/[^\\\$']+/, Str
        mixin :string
      end

      state :import do
        rule %r/;/, Operator, :pop!
        rule %r/(?:show|hide)\b/, Keyword::Declaration
        mixin :root
      end

      state :string do
        mixin :interpolation
        rule %r/\\[nrt\"\'\\]/, Str::Escape
      end

      state :interpolation do
        rule %r/\$#{id}/, Str::Interpol
        rule %r/\$\{[^\}]+\}/, Str::Interpol
      end
    end
  end
end
