# OpenLCB standards & documentation repository

## Repository hierarchy

- `standards` directory contains the adopted OpenLCB standards and the matching
  technical notes, with the version that is adopted.
  
- `standards/lcc` directory contains the cover pages for the NMRA and the
  document versions submitted to the NMRA. These are always the same version as
  the adopted OpenLCB standard at the time plus the additional title page.
  
- `drafts` directory contains the next version of adopted standards, as well as
  work in progress documents for protocols that were not adopted yet.
  
- `schema` directory contains machine-readable standards used for validating
  the correctness for XML documents.
  
- `archive` directory contains a variety prior artifacts (some of these might
  be outdated); includes some articles intended for users, an older versions of
  the web site, text files with collection of ideas and correspondence with the
  NMRA.

- `bin` directory contains scripts and rules for generated artifacts.

## Process to update

### For authors

If you need to change any standards or drafts, use the following process:

0. Create a new branch, either in your fork or in the main repository; prefixed
   with your username, e.g. bracz-fix-typo-in-message-network-std.

1. Make your edits in the ODT file. When you are in the drafts folder, make
   sure to have "Edit > Change tracking > Record" and "Show changes" both on.

2. Update the last changed date in menu > File > Properties, select Custom
   Properties, and update OlcbDate to the current date.
   
4. Save the ODT file and exit soffice.

5. Run make in the respective directory (standards or drafts) to generate PDF
   and TXT output.

6. Commit all three files (ODT, PDF, TXT).

7. Create a pull request. Make sure the pull request sets the "allow changes by
   the maintainer of the repository" if you are working from a fork. Assign
   reviewers from the repository maintainers to check your changes.

8. Wait for the reviewer to approve the change. Fix any comments. If the ODT
   file is changed, run `make` again to re-generate the PDF and TXT files.

If you are unable to run `make`, ask a repository maintainer or help on the
openlcb@groups.io mailing list for someone to generate the PDF and TXT files
and push them to your branch.

### For repository maintainers

When merging a PR, always use the "squash and merge" method in GitHub.

In order to successfully run `make`, you need the following packages besides
libreoffice:

```
sudo apt-get install writer2latex links
```

## Process for adopting a document

The technical process for adopting a document is described in
[standards/Conventions.html](standards/Conventions.html).

### Forking an adopted document to drafts

1. Perform a git copy with history for all three of these files *on the master branch*:

```
git checkout master
git cp standards/FooStandardS.odt drafts/FooStandardS.odt
git cp standards/FooStandardS.pdf drafts/FooStandardS.pdf
git cp standards/generated/FooStandardS.txt drafts/generated/FooStandardS.txt
git push
```
  
  The git-cp script is available in the bin directory to do this. The script
  will only work if your repository is clean.
  
  This step can only be done by a maintainer -- ask someone on the
  openlcb@groups.io list.

1. Create a new branch:

```
git checkout master
git pull
git checkout -b bracz-fork-foo-standard-to-draft
```

2. Add the DRAFT watermark:

  - open drafts/StandardS.odt with openoffice
  - menu > format > watermark...
  - set watermark to DRAFT, font to Times New Roman, 45 degree, 50% gray.

3. Update state

  - menu > File > Properties
  - select Custom Properties
  - update OlcbStatus to Draft, and OlcbDate to the current date

4. Save the odt file, quit openoffice. Run `make` in the drafts folder.

5. Commit all your changes (to the bracz-fork-foo-standard-to-draft branch)

  - Note that there should be no changes to the generated/FooStandardS.txt at
    this point.

6. Push the branch to github. Create a PR. Ask for review.

7. Ask a maintainer to merge the PR. IMPORTANT: the PR MUST be merged using the
   "Create a Merge Commit" method.


### Adopting a document from Drafts into Standards

1. Create a new branch for the adoption process.

```
git checkout master
git pull
git checkout -b bracz-adopt-foo-standard
```

2. Check the changes for gross errors

  - Review all changes in menu > Edit > Track Changes > Manage...
  - Do not accept changes at this point.

3. Update the ODT file to mark it as adopted

  - In menu > File > Properties > Custom Properties, set OlcbStatus to Adopted,
    the date, and the year as a range up till now, e.g. 2013-2021.
  - menu > Format > Watermark..., replace the DRAFT text with a single space.
  - Make sure that View -> Show Tracked Changes is still on at this point.

4. Save the ODT file, quit openoffice. Run `make` to generate the PDF and TXT
   output.

  - This creates a PDF file with changes marked.  Copy that to the 
    standards/changes directory. Name it with the date, 
    e.g. FooStandardS-changes-2024-07-22.pdf
  - Add that copy and commit it with all other changed files (odt, pdf, txt) to
    your branch with comment "adopted with changes present"
    
5. Update the ODT file to remove change markers

  - Accept all changes. There should be no changes left in the Manage changes
    dialog.
  - Check for comment bubbles.  If any are found, remove them.
  - Update the Table of Contents
    
6. Save the ODT file, quit openoffice. Run `make` to generate the PDF and TXT
   output.
   
7. Commit the odt, pdf, txt files to your branch 
   with comment "adopted with changes accepted"

8. Delete the files from the standards directory. This step should be skipped
   if we are adopting a document for the first time, and there are no previous
   adopted files.

```
git rm standards/FooStandardS.odt
git rm standards/FooStandardS.pdf
git rm standards/generated/FooStandardS.txt
```

9. Commit these to your branch with comment "previous files removed"

10. Move the files from draft to standards.

```
git mv {drafts,standards}/FooStandardS.odt
git mv {drafts,standards}/FooStandardS.pdf
git mv {drafts,standards}/generated/FooStandardS.txt
```

11. Commit these to your branch with comment "move adopted files to standards/"

12. Create a PR with your changes on GitHub. Ask a maintainer to review the PR.

13. IMPORTANT: The PR must be merged with a "Create a merge commit" option.

## Docker
A Docker image file has been added to provide a consistent file conversion processing environment in a Docker Container.

To build your own Docker execute the following command in a bash shell:

```
docker build -t openlcb-doc-build-env --build-arg USER_ID=$(id -u) --build-arg GROUP_ID=$(id -g) . 
```

To run your own Docker execute the following command in a bash shell:

```
docker run -it --rm -v $PWD:/home/openlcb -u $(id -u):$(id -g) openlcb-doc-build-env
```
