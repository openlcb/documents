RUNNING := $(shell ps ax | grep -v grep | grep soffice)

OFFICE = soffice
SINPUT = $(wildcard *.ods)
TINPUT = $(wildcard *.odt)
OUTPUT = $(SINPUT:.ods=.pdf) $(TINPUT:.odt=.pdf)

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
all: pdf

#all:
#	@echo $(SSRC)
#	@echo $(TSRC)
#	@echo $(OUTPUT)


.PHONY: pdf
pdf: $(OUTPUT)

.SUFFIXES:
.SUFFIXES: .odt .ods .pdf

# Spreadsheets
.ods.pdf:
	$(OFFICE) --headless --convert-to pdf $<

# test documents
.odt.pdf:
	$(OFFICE) --headless --convert-to pdf $<

.PHONY: veryclean
veryclean: clean

.PHONY: clean
clean:
	rm -rf *.pdf
endif

