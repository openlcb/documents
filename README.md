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
   
2. Save the ODT file and exit soffice.

3. Run make in the respective directory (standards or drafts) to generate PDF
   and TXT output.

4. Commit all three files (ODT, PDF, TXT).

5. Create a pull request. Make sure the pull request sets the "allow changes by
   the maintainer of the repository" if you are working from a fork. Assign
   reviewers from the repository maintainers to check your changes.

6. Wait for the reviewer to approve the change. Fix any comments. If the ODT
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
