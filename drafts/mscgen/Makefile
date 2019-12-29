MSCGEN = mscgen
INPUT = $(wildcard *.msc)
OUTPUT = $(INPUT:.msc=.png)

.PHONY: all
all: mscgen

.PHONY: mscgen
mscgen: $(OUTPUT)

.SUFFIXES:
.SUFFIXES: .msc .png

.msc.png:
	$(MSCGEN) -T png -i $< -o $@

.PHONY: veryclean
veryclean: clean

.PHONY: clean
clean:
	rm -rf *.png
