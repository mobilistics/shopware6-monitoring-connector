#!/usr/bin/env sh

PLUGIN_ROOT="$(realpath "$(dirname "$(realpath "$0")")"/../..)"

: "${SHOPWARE_VERSION:="v6.5.5"}"
: "${PLUGIN_NAME:="MobiMamoConnector"}"

DOCKER_IMAGE=ghcr.io/friendsofshopware/platform-plugin-dev:${SHOPWARE_VERSION}

CI_BIN_DIR=$(realpath "${PLUGIN_ROOT}/bin/ci/docker")

(
	cd "${PLUGIN_ROOT}" || exit 1

	docker run \
		-v "${CI_BIN_DIR}/test.sh:/usr/local/bin/test.sh" \
		-v "${PLUGIN_ROOT}:/plugins/${PLUGIN_NAME}" \
		-e PLUGIN_NAME=${PLUGIN_NAME} \
		${DOCKER_IMAGE} \
		sh /usr/local/bin/test.sh
)
