#!/bin/bash
npm install --global yarn
# npm install -g npm@5.3.0
# npm cache clean
# npm install
yarn install
bower install --allow-root
ember build --watch
