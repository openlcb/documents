FROM ubuntu:20.04

# Based on: https://github.com/ipunkt/docker-libreoffice-headless

LABEL maintainer="kiwi64ajs@gmail.com" version.ubuntu="20.04"

ENV DEBIAN_FRONTEND noninteractive

RUN apt update && \
	apt -y -q install \
		libreoffice \
		libreoffice-writer \
		ure \
		libreoffice-java-common \
		libreoffice-core \
		libreoffice-common \
		openjdk-8-jre \
		fonts-opensymbol \
		hyphen-fr \
		hyphen-de \
		hyphen-en-us \
		hyphen-it \
		hyphen-ru \
		fonts-dejavu \
		fonts-dejavu-core \
		fonts-dejavu-extra \
		fonts-droid-fallback \
		fonts-dustin \
		fonts-f500 \
		fonts-fanwood \
		fonts-freefont-ttf \
		fonts-liberation \
		fonts-lmodern \
		fonts-lyx \
		fonts-sil-gentium \
		fonts-texgyre \
		fonts-tlwg-purisa \
		writer2latex links make git texlive-latex-base texlive-latex-extra pdftk locales && \
	apt-get -y -q remove libreoffice-gnome && \
	apt -y autoremove && \
	rm -rf /var/lib/apt/lists/*

RUN adduser --home=/opt/libreoffice --disabled-password --gecos "" --shell=/bin/bash libreoffice

VOLUME ["/tmp"]
WORKDIR "/tmp"

RUN locale-gen en_US.UTF-8 && update-locale LC_ALL=en_US.UTF-8 LANG=en_US.UTF-8

ENV LANG en_US.UTF-8  
ENV LANGUAGE en_US:en  
ENV LC_ALL en_US.UTF-8

ARG USER_ID
ARG GROUP_ID

RUN useradd -d /home/openlcb -u ${USER_ID} -g ${GROUP_ID} openlcb

RUN gpasswd -a openlcb sudo

WORKDIR /home/openlcb
ENTRYPOINT bash
