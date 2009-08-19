#!/bin/sh

# retrieve po files from c2ci18n and copy them to the right place

PODIRECTORY='../apps/frontend/i18n/'
I18NSITE='http://c2ci18n.appspot.com/'

for i in fr it de en es ca eu
do
  wget ${I18NSITE}po/messages.${i}.po -O ${PODIRECTORY}messages.${i}.po
done
