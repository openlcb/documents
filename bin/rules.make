
RUNNING := $(shell ps ax | grep -v grep | grep soffice | grep -v quickstart )

OFFICE := $(shell sh -c \
            "if [ -d /Applications/LibreOffice.app/Contents/MacOS ]; then echo /Applications/LibreOffice.app/Contents/MacOS/soffice; \
           elif [ -d /Applications/OpenOffice.app/Contents/MacOS ]; then echo /Applications/OpenOffice.app/Contents/MacOS/soffice; \
           else echo soffice; fi" \
           )

ifneq ($(RUNNING),)
.PHONY: all
all:
	@echo $(test)
	@echo "************************************************************"
	@echo "* error!!!  office suite running                           *"
	@echo "* please close all instances of office suite and try again *"
	@echo "************************************************************"
else
.PHONY: all
all: pdf txt

endif

.SUFFIXES:
.SUFFIXES: .odt .ods .pdf .txt

# Spreadsheets
.ods.pdf:
	$(OFFICE) --headless --convert-to pdf $<

# text documents
.odt.pdf:
	$(OFFICE) --headless --convert-to pdf $<

$(TXTOUTPUT): generated/%.txt : %.odt
	$(REPOROOT)/bin/odt2txt "$<" "$@"

.PHONY: veryclean
veryclean: clean

.PHONY: clean
clean:
	rm -rf *.pdf
