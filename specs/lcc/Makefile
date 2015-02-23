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
#  $(1) is the composite name of the file like 9.7.0.1-S
#  $(2) is the designation S or TN
#  $(3) is the full name of the source pdf (example MemoryConfigurationS.pdf)
#  $(4) is the output filename
define DOCU_template

obj/$(1)-header.tex: header_template.tex
	cat $$< > $$@
	echo Adopted as NMRA $(if $(filter $(2),TN),Technical Note,Standard) $(1) >> $$@
	echo '\end{document}' >> $$@


obj/$(1)-header.pdf: obj/$(1)-header.tex
	cd obj ; pdflatex ../$$<


obj/$(1)-body.pdf: $(3) obj/$(1)-header.pdf
	pdftk $$< multibackground obj/$(1)-header.pdf output $$@


$(4): obj/$(1)-Cover.pdf obj/$(1)-body.pdf 
	pdftk $$+ cat output $$@

all: $(4)

endef


DATE=2015-02-17

# Helper function for emitting targets.
#
# arguments:
#  $(1) is the basename of the file which is like 9.7.0.1
#  $(2) is the designation S or TN (also part of the filename)
#  $(3) is the basename of the source pdf (example MemoryConfiguration)
DOCU_call=$(call DOCU_template,$(1)-$(2),$(2),$(3)$(2).pdf,$(2)-$(1)-$(3)-$(DATE).pdf)

DOCU_stn=$(call DOCU_template,$(1)-S,S,$(2)S.pdf,S-$(1)$(2)-$(DATE).pdf) $(call DOCU_template,$(1)-TN,TN,$(2)TN.pdf,TN-$(1)-$(2)-$(DATE).pdf)


$(eval $(call DOCU_call,9.7.0.1,TN,Glossary))
$(eval $(call DOCU_call,9.7.0.2,TN,CommonInformation))
$(eval $(call DOCU_stn,9.7.0.3,UniqueIdentifiers))
$(eval $(call DOCU_stn,9.7.0.4,EventIdentifiers))
$(eval $(call DOCU_stn,9.7.1.1,CanPhysical))
#$(eval $(call DOCU_stn,9.7.1.2,TcpPhysical)) does not exist yet
$(eval $(call DOCU_stn,9.7.2.1,CanFrameTransfer))
#$(eval $(call DOCU_stn,9.7.2.2,TcpTransfer)) does not exist yet
$(eval $(call DOCU_stn,9.7.3.1,EventTransport))
$(eval $(call DOCU_stn,9.7.3.2,DatagramTransport))
#$(eval $(call DOCU_stn,9.7.3.3,StreamTransport)) does not exist yet
$(eval $(call DOCU_stn,9.7.3,MessageNetwork))

# todo: shouldn't this be in the 9.7.4 range?
$(eval $(call DOCU_stn,9.7.3.4,SimpleNodeInformation))

$(eval $(call DOCU_stn,9.7.4.1,ConfigurationDescriptionInformation))
$(eval $(call DOCU_stn,9.7.4.2,MemoryConfiguration))


