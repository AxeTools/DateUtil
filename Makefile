###############################################################################
#     _             _____           _
#    / \   __  ____|_   _|__   ___ | |___
#   / _ \  \ \/ / _ \| |/ _ \ / _ \| / __|
#  / ___ \  >  <  __/| | (_) | (_) | \__ \
# /_/   \_\/_/\_\___||_|\___/ \___/|_|___/
#
# https://github.com/AxeTools
# AxeTools/BitFlagTrait
#
###############################################################################
.DEFAULT_GOAL := default

#
# Bring up the dev containers
#
up:
	@echo "##### Bringing up Dev Containers #####"
	@(docker compose up -d)

#
# Bring down the dev containers
#
down:
	@echo "##### Bringing down Dev Containers #####"
	@(docker compose down)

#
# Execute a Bash terminal on the dev php container
#
bash: up
	@echo "##### Dev php Container Bash Prompt #####"
	@(docker compose exec php bash)

#
# Execute tests against the dev php container
#
tests: up
	@echo "##### Dev php Container Tests #####"
	@(docker compose exec php composer tests)


#
# Install the composer assets
#
install: up
	@echo "##### Installing Composer Dependencies #####"
	@(docker compose exec php composer install)

#
# Build the dev docker file
#
build:
	@echo "##### Building Production Containers #####"
	@docker compose build php

#
# build and bring up the dev containers and install assets
#
default: build install