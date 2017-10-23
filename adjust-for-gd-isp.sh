#!/bin/bash



##----------------------------------------------------------------------
##  2017-10-23 TO-DO:  add notes to explain how to create complete and
##   correct file list of local PHP project files, so that all files
##   have their require_once() paths updated, that is, no files are
##   missed.  As of this writing, PHP files listed below which require
##   others in the group are all in a single directory, but paths are
##   expressed as full absolute paths.  For this reason installation
##   of these local PHP libraries in a differing directory requires
##   updating the expressed require_once() paths.                 - TMH
##----------------------------------------------------------------------

file_list="\
defines-nn.php \
diagnostics-nn.php \
file-and-directory-routines.php \
hello-world.php \
page-building-routines.php \
site-navigation-routines.php \
"

# path_old=/path/to/local/library/on/source/host
# path_new=/path/to/local/library/on/destination/host

# Some possible library paths include /usr/lib/project_name, /var/lib/project_name, /opt/project_name/lib, /home/user/lib, 



echo "$0:  starting,"

echo "$0:  before substitutions checking for old path,"
echo "$0:  calling grep . . ."
grep -n $path_old ./*.php

for file in ${file_list}; do
   echo ${file}
   sed -i s@$path_old@$path_new@ ${file}
done

echo "$0:  after substitutions checking for old path,"
echo "$0:  calling grep . . ."
grep -n $path_old ./*.php

echo "$0:  done."
echo

exit 0
