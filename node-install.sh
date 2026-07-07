#!/bin/bash

echo "installing node 18"

curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash

source ~/.bashrc

nvm install 18

cd httpdocs/staging/app/design/frontend/beautybop/bbop

npm run build

echo "finished install node 18"