#!/usr/bin/env python

import re
import os
import shutil

frontmatter = "---\n{0}\n---\n"

replacer = re.compile('```php(.+?)```', re.I | re.DOTALL)
title_finder = re.compile('(.+)\n=+\n', re.I)
linkfinder = re.compile('\S+\.md', re.I)

def clean_directory(directory):
	shutil.rmtree(directory)

def get_filemap(base, outdir):
	filemap = {}
	for dirname, dirnames, filenames in os.walk(base):
		outputdir = outdir

		# Where are we going?
		relative_dir = os.path.relpath(dirname, base)
		if relative_dir != '.':
			outputdir = os.path.join(outdir, relative_dir)

		# Ensure the directory exists first
		if not os.path.exists(outputdir):
			os.makedirs(outputdir)

		for filename in filenames:
			# What's the actual input file?
			inputfile = os.path.join(dirname, filename)

			# Where are we putting it?
			outputfile = filename
			if outputfile == 'README.md':
				outputfile = 'index.md'
			outputfile = os.path.join(outputdir, outputfile)

			filemap[inputfile] = outputfile

	return filemap

def compile_file(inputfile, outputfile, frontmatter):
	with open(inputfile, 'r') as content_file:
		contents = content_file.read()

		# Correct highlighter
		contents = replacer.sub(r'{% highlight php startinline %}\1{% endhighlight %}', contents)

		# Replace internal links
		links = linkfinder.findall(contents)
		for link in links:
			if link.startswith('http://') or link.startswith('https://'):
				continue

			genlink = link.replace('README.md', 'index.md').replace('.md', '.html')
			contents = contents.replace(link, genlink)

		# Find title if possible
		if not 'title' in frontmatter:
			title = title_finder.search(contents)
			if title:
				frontmatter['title'] = title.group(1)
		
		# Generate frontmatter
		frontmatter_vars = ['{0}: {1}'.format(key, val) for key, val in frontmatter.iteritems()]
		frontmatter_content = '---\n{0}\n---\n'.format('\n'.join(frontmatter_vars))
		contents = "{0}{1}".format(frontmatter_content, contents)

		# Write output
		with open(outputfile, 'w') as output:
			output.write(contents)

def compile_docs(inputdir, outputdir, template='documentation'):
	inputdir = os.path.realpath(inputdir)
	outputdir = os.path.realpath(outputdir)
	# clean_directory(outputdir)
	files = get_filemap(inputdir, outputdir)

	for docfile, outfile in files.iteritems():
		compile_file(docfile, outfile, {'layout': 'documentation'})

compile_docs('../docs', './docs')
compile_file('../README.md', './index.md', {'layout': 'home', 'title': ''})