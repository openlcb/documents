RUNNING := $(shell ps ax | grep -v grep | grep soffice)

OFFICE := $(shell sh -c \
            "if [ -d /Applications/LibreOffice.app/Contents/MacOS ]; then echo /Applications/LibreOffice.app/Contents/MacOS/soffice; \
           elif [ -d /Applications/OpenOffice.app/Contents/MacOS ]; then echo /Applications/OpenOffice.app/Contents/MacOS/soffice; \
           else echo soffice; fi" \
           )
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



.PHONY: pdf
pdf: $(OUTPUT)

.SUFFIXES:
.SUFFIXES: .odt .ods .pdf .txt .html

# Spreadsheets
.ods.pdf:
	$(OFFICE) --headless --convert-to pdf $<

#.ods.txt:
#	$(OFFICE) --headless --convert-to txt:Text $<

.ods.html:
	$(OFFICE) --headless --convert-to html $<


# text documents
.odt.pdf:
	$(OFFICE) --headless --convert-to pdf $<

.odt.html:
	$(OFFICE) --headless --convert-to html $<

.html.txt:
	links -dump -width 120 $< > $@

#	$(OFFICE) --headless --convert-to txt:Text $<

.PHONY: veryclean
veryclean: clean

.PHONY: clean
clean:
	rm -rf *.pdf
endif

