#/bin/bash

ruby -rwebrick -e'WEBrick::HTTPServer.new(:Port => 4000, :DocumentRoot => Dir.pwd).start'

