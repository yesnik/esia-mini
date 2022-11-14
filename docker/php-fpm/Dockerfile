FROM php:8.1-fpm-bullseye

RUN apt-get update && apt-get install wget unzip openssl libssl-dev -y

# Install GOST-engine for OpenSSL
# Instruction: https://github.com/gost-engine/engine/blob/openssl_1_1_1/INSTALL.md
ARG ENGINES_DIR="/usr/lib/x86_64-linux-gnu/engines-1.1"
ARG OPENSSL_LIB_DIR="/usr/lib/ssl"
# Commit May 20, 2022
# URL: https://github.com/gost-engine/engine/archive/739f957615eb33a33a6485ae7cf29c7c679fd59a.zip
ARG GOST_ENGINE_VERSION=739f957615eb33a33a6485ae7cf29c7c679fd59a
ARG GOST_ENGINE_SHA256="99e047a239b374b62edd5e543cd76ac15f85b58adadc18f59f962e65008d126d"
RUN apt-get install cmake -y \
  && cd /usr/local/src \
  && wget "https://github.com/gost-engine/engine/archive/${GOST_ENGINE_VERSION}.zip" -O gost-engine.zip \
  && echo "$GOST_ENGINE_SHA256" gost-engine.zip | sha256sum -c - \
  && unzip gost-engine.zip -d ./ \
  && cd "engine-${GOST_ENGINE_VERSION}" \
  && mkdir build \
  && cd build \
  && cmake -DCMAKE_BUILD_TYPE=Release \
    -DOPENSSL_ROOT_DIR=${OPENSSL_LIB_DIR} \
    -DOPENSSL_LIBRARIES=${OPENSSL_LIB_DIR} -DOPENSSL_ENGINES_DIR=${ENGINES_DIR} .. \
  && cmake --build . --config Release \
  && make install \
  && rm -rf "/usr/local/src/*"

COPY ./gost-engine.conf /usr/local/src/gost-engine.conf

# Edit openssl.conf to enable GOST engine
ARG OPENSSL_CONFIG_PATH="${OPENSSL_LIB_DIR}/openssl.cnf"
RUN sed -i 's/openssl_conf = default_conf/openssl_conf = openssl_def/g' $OPENSSL_CONFIG_PATH \
  && cat /usr/local/src/gost-engine.cnf >> $OPENSSL_CONFIG_PATH

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini

WORKDIR /app
