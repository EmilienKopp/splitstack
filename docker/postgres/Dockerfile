FROM pgvector/pgvector:pg17

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        build-essential \
        ca-certificates \
        git \
        postgresql-server-dev-17 \
    && git clone --depth 1 https://github.com/timescale/pg_textsearch.git /tmp/pg_textsearch \
    && cd /tmp/pg_textsearch \
    && make \
    && make install \
    && cd / \
    && rm -rf /tmp/pg_textsearch \
    && apt-get purge -y build-essential git postgresql-server-dev-17 \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*
