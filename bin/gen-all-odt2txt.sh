#!/bin/bash
#
# This script was used to generate a matching TXT file for each ODT document in
# the history of the git repository. It is expected to be called from the top
# of the git repository directory, finds all ODT files, and turns each of them
# into a generated/filename.txt file.
#
# Usage:
# FILTER_BRANCH_SQUELCH_WARNING=1 git filter-branch --tree-filter gen-all-odt2txt.sh

find . -name *.odt -print0 | xargs -0 -n 1 -P 16 odt2txt
