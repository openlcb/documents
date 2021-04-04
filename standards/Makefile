REPOROOT=$(abspath ..)

SINPUT = $(wildcard *.ods)
TINPUT = $(wildcard *.odt)
PDFOUTPUT = $(SINPUT:.ods=.pdf) $(TINPUT:.odt=.pdf)
TXTOUTPUT = $(TINPUT:%.odt=generated/%.txt)

include $(REPOROOT)/bin/rules.make

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

