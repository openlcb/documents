RUNNING := $(shell ps ax | grep -v grep | grep soffice | grep -v quickstart )

OFFICE := $(shell sh -c \
            "if [ -d /Applications/LibreOffice.app/Contents/MacOS ]; then echo /Applications/LibreOffice.app/Contents/MacOS/soffice; \
           elif [ -d /Applications/OpenOffice.app/Contents/MacOS ]; then echo /Applications/OpenOffice.app/Contents/MacOS/soffice; \
           else echo soffice; fi" \
           )
SINPUT = $(wildcard *.ods)
TINPUT = $(wildcard *.odt)
PDFOUTPUT = $(SINPUT:.ods=.pdf) $(TINPUT:.odt=.pdf)
TXTOUTPUT = $(TINPUT:%.odt=generated/%.txt)


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

#all:
#	@echo $(SSRC)
#	@echo $(TSRC)
#	@echo $(OUTPUT)

nmra: \
	UniqueIdentifiersS.pdf UniqueIdentifiersTN.pdf \
	GlossaryTN.pdf CommonInformationTN.pdf \
	MessageNetworkS.pdf MessageNetworkTN.pdf  \
	EventIdentifiersS.pdf EventIdentifiersTN.pdf \
	EventTransportS.pdf EventTransportTN.pdf \
	DatagramTransportS.pdf DatagramTransportTN.pdf \
	ConfigurationDescriptionInformationS.pdf ConfigurationDescriptionInformationTN.pdf \
	MemoryConfigurationS.pdf MemoryConfigurationTN.pdf \
	SimpleNodeInformationS.pdf SimpleNodeInformationTN.pdf



.PHONY: pdf txt
pdf: $(PDFOUTPUT)

txt: $(TXTOUTPUT)

.SUFFIXES:
.SUFFIXES: .odt .ods .pdf .txt

# Spreadsheets
.ods.pdf:
	$(OFFICE) --headless --convert-to pdf $<

# text documents
.odt.pdf:
	$(OFFICE) --headless --convert-to pdf $<

$(TXTOUTPUT): generated/%.txt : %.odt
	TMPDIR=`mktemp -d` && soffice --headless --convert-to html --outdir $$TMPDIR  $< && links -dump -width 110 $$TMPDIR/$*.html > $@ && rm -rf $$TMPDIR

.PHONY: veryclean
veryclean: clean

.PHONY: clean
clean:
	rm -rf *.pdf
endif

