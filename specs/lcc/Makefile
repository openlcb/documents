BRUNNING := $(shell ps ax | grep -v grep | grep soffice)

OFFICE := $(shell sh -c \
            "if [ -d /Applications/LibreOffice.app/Contents/MacOS ]; then echo /Applications/LibreOffice.app/Contents/MacOS/soffice; \
           elif [ -d /Applications/OpenOffice.app/Contents/MacOS ]; then echo /Applications/OpenOffice.app/Contents/MacOS/soffice; \
           else echo soffice; fi" \
           )

.PHONY: all clean
all: obj


obj:
	mkdir obj

clean:
	rm -rf obj
	rm -f S-9.7.*
	rm -f TN-9.7.*

template.pdf: template.tex
	pdflatex $<


merged.pdf: template.pdf source.pdf
	pdftk source.pdf multibackground template.pdf output $@


obj/%.pdf : %.odt
	@[ -z "$(RUNNING)" ] || (echo office suite already running. close and try again ; exit 1)
	$(OFFICE) --headless --convert-to pdf --outdir obj $<

FILEBASE:=$$(1)-$$(2)
SRCFILE:=$$(3)$$(2).pdf

# This function emits the make targets for all the intermediate files for a
# specific documentation target.
#
# arguments:
#  $(1) is the composite name of the file like 9.7.0.1-S, used for the temporary filenaming
#  $(2) is the designation S or TN
#  $(3) is the full name of the source pdf (example MemoryConfigurationS.pdf)
#  $(4) is the output filename (like S-9.7.0.1-MemoryConfigurationS-2015-02-17.pdf)
#  $(5) is the document number
#  $(6) is "Adopted" or "Draft"
define DOCU_template

obj/$(1)-header.tex: header_template.tex
	cat $$< > $$@
	echo $(if $(filter $(6),Draft),Submitted for public comment,Adopted) as NMRA $(if $(filter $(2),TN),Technical Note,Standard) $(2)-$(5) >> $$@
	echo '\end{document}' >> $$@


obj/$(1)-header.pdf: obj/$(1)-header.tex
	cd obj ; pdflatex ../$$<


obj/$(1)-body.pdf: $(3) obj/$(1)-header.pdf
	pdftk $$< multibackground obj/$(1)-header.pdf output $$@


$(4): obj/$(1)-Cover.pdf obj/$(1)-body.pdf 
	pdftk $$+ cat output $$@

all: $(4)

endef

# Helper function for emitting targets.
#
# arguments:
#  $(1) is the basename of the file which is like 9.7.0.1
#  $(2) is the designation S or TN (also part of the filename)
#  $(3) is the basename of the source pdf (example MemoryConfiguration)
#  $(4) is the output date (example 2016-02-08)
#  $(5) is "Adopted" or "Draft"

DOCU_call=$(call DOCU_template,$(1)-$(2)$(if $(filter $(5),Draft),-Draft,),$(2),$(3)$(2)-$(4).pdf,$(2)-$(1)-$(3)-$(4)$(if $(filter $(5),Draft),-Draft,).pdf,$(1),$(5))

# Helper function for emitting two targets, an S and a TN.
#
# arguments:
#  $(1) is the basename of the file which is like 9.7.0.1
#  $(2) is the basename of the source pdf (example MemoryConfiguration)
#  $(3) is the output date (example 2016-02-08)
#  $(4) is "Adopted" (default if empty), or "Draft"

DOCU_stn=$(call DOCU_call,$(1),S,$(2),$(3),$(4))  $(call DOCU_call,$(1),TN,$(2),$(3),$(4))

$(eval $(call DOCU_call,9.7.0.1,TN,Glossary,2016-02-06))
$(eval $(call DOCU_call,9.7.0.2,TN,CommonInformation,2015-02-17))
$(eval $(call DOCU_stn,9.7.0.3,UniqueIdentifiers,2016-02-06))
$(eval $(call DOCU_stn,9.7.0.4,EventIdentifiers,2016-02-06))
$(eval $(call DOCU_stn,9.7.1.1,CanPhysical,2015-02-17))
$(eval $(call DOCU_stn,9.7.1.1,CanPhysical,2016-02-08,Draft))
#$(eval $(call DOCU_stn,9.7.1.2,TcpPhysical)) does not exist yet
$(eval $(call DOCU_stn,9.7.2.1,CanFrameTransfer,2016-02-06))
#$(eval $(call DOCU_stn,9.7.2.2,TcpTransfer)) does not exist yet
$(eval $(call DOCU_stn,9.7.3.1,EventTransport,2016-02-06))
$(eval $(call DOCU_stn,9.7.3.2,DatagramTransport,2016-02-06))
#$(eval $(call DOCU_stn,9.7.3.3,StreamTransport)) does not exist yet
$(eval $(call DOCU_stn,9.7.3,MessageNetwork,2016-02-06))

$(eval $(call DOCU_stn,9.7.4.1,ConfigurationDescriptionInformation,2016-02-06))
$(eval $(call DOCU_stn,9.7.4.2,MemoryConfiguration,2016-02-06))
$(eval $(call DOCU_stn,9.7.4.3,SimpleNodeInformation,2016-02-06))
$(eval $(call DOCU_stn,9.7.4.4,FirmwareUpgrade,2016-02-08,Draft))


