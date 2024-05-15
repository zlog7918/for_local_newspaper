# FROM debian:stretch-20211011-slim
# # ADD file:1220579e31585dec45ca8e35874eb689018ed026a1f23b7906f791c0279671e0 in /
# RUN set -ex;  if ! command -v gpg > /dev/null; then   apt-get update;   apt-get install -y --no-install-recommends    gnupg    dirmngr   ;   rm -rf /var/lib/apt/lists/*;  fi
# RUN set -eux;  groupadd -r postgres --gid=999;  useradd -r -g postgres --uid=999 --home-dir=/var/lib/postgresql --shell=/bin/bash postgres;  mkdir -p /var/lib/postgresql;  chown -R postgres:postgres /var/lib/postgresql
# ENV GOSU_VERSION=1.12
# RUN set -eux;  savedAptMark="$(apt-mark showmanual)";  apt-get update;  apt-get install -y --no-install-recommends ca-certificates wget;  rm -rf /var/lib/apt/lists/*;  dpkgArch="$(dpkg --print-architecture | awk -F- '{ print $NF }')";  wget -O /usr/local/bin/gosu "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch";  wget -O /usr/local/bin/gosu.asc "https://github.com/tianon/gosu/releases/download/$GOSU_VERSION/gosu-$dpkgArch.asc";  export GNUPGHOME="$(mktemp -d)";  gpg --batch --keyserver hkps://keys.openpgp.org --recv-keys B42F6819007F00F88E364FD4036A9C25BF357DD4;  gpg --batch --verify /usr/local/bin/gosu.asc /usr/local/bin/gosu;  gpgconf --kill all;  rm -rf "$GNUPGHOME" /usr/local/bin/gosu.asc;  apt-mark auto '.*' > /dev/null;  [ -z "$savedAptMark" ] || apt-mark manual $savedAptMark > /dev/null;  apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false;  chmod +x /usr/local/bin/gosu;  gosu --version;  gosu nobody true
# RUN set -eux;  if [ -f /etc/dpkg/dpkg.cfg.d/docker ]; then   grep -q '/usr/share/locale' /etc/dpkg/dpkg.cfg.d/docker;   sed -ri '/\/usr\/share\/locale/d' /etc/dpkg/dpkg.cfg.d/docker;   ! grep -q '/usr/share/locale' /etc/dpkg/dpkg.cfg.d/docker;  fi;  apt-get update; apt-get install -y --no-install-recommends locales; rm -rf /var/lib/apt/lists/*;  localedef -i en_US -c -f UTF-8 -A /usr/share/locale/locale.alias en_US.UTF-8
# ENV LANG=en_US.utf8
# RUN set -eux;  apt-get update;  apt-get install -y --no-install-recommends   libnss-wrapper   xz-utils  ;  rm -rf /var/lib/apt/lists/*
# RUN mkdir /docker-entrypoint-initdb.d
# RUN set -ex;  key='B97B0AFCAA1A47F044F244A07FCC7D46ACCC4CF8';  export GNUPGHOME="$(mktemp -d)";  gpg --batch --keyserver keyserver.ubuntu.com --recv-keys "$key";  gpg --batch --export "$key" > /etc/apt/trusted.gpg.d/postgres.gpg;  command -v gpgconf > /dev/null && gpgconf --kill all;  rm -rf "$GNUPGHOME";  apt-key list
# ENV PG_MAJOR=9.6
# ENV PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/lib/postgresql/9.6/bin
# ENV PG_VERSION=9.6.23-1.pgdg90+1
# RUN set -ex;   export PYTHONDONTWRITEBYTECODE=1;   dpkgArch="$(dpkg --print-architecture)";  case "$dpkgArch" in   amd64 | i386 | ppc64el)    echo "deb http://apt.postgresql.org/pub/repos/apt/ stretch-pgdg main $PG_MAJOR" > /etc/apt/sources.list.d/pgdg.list;    apt-get update;    ;;   *)    echo "deb-src http://apt.postgresql.org/pub/repos/apt/ stretch-pgdg main $PG_MAJOR" > /etc/apt/sources.list.d/pgdg.list;       tempDir="$(mktemp -d)";    cd "$tempDir";       savedAptMark="$(apt-mark showmanual)";       apt-get update;    apt-get build-dep -y     postgresql-common pgdg-keyring     "postgresql-$PG_MAJOR=$PG_VERSION"    ;    DEB_BUILD_OPTIONS="nocheck parallel=$(nproc)"     apt-get source --compile      postgresql-common pgdg-keyring      "postgresql-$PG_MAJOR=$PG_VERSION"    ;       apt-mark showmanual | xargs apt-mark auto > /dev/null;    apt-mark manual $savedAptMark;       ls -lAFh;    dpkg-scanpackages . > Packages;    grep '^Package: ' Packages;    echo "deb [ trusted=yes ] file://$tempDir ./" > /etc/apt/sources.list.d/temp.list;    apt-get -o Acquire::GzipIndexes=false update;    ;;  esac;   apt-get install -y --no-install-recommends postgresql-common;  sed -ri 's/#(create_main_cluster) .*$/\1 = false/' /etc/postgresql-common/createcluster.conf;  apt-get install -y --no-install-recommends   "postgresql-$PG_MAJOR=$PG_VERSION"   "postgresql-contrib-$PG_MAJOR=$PG_VERSION"  ;   rm -rf /var/lib/apt/lists/*;   if [ -n "$tempDir" ]; then   apt-get purge -y --auto-remove;   rm -rf "$tempDir" /etc/apt/sources.list.d/temp.list;  fi;   find /usr -name '*.pyc' -type f -exec bash -c 'for pyc; do dpkg -S "$pyc" &> /dev/null || rm -vf "$pyc"; done' -- '{}' +;   postgres --version
# RUN set -eux;  dpkg-divert --add --rename --divert "/usr/share/postgresql/postgresql.conf.sample.dpkg" "/usr/share/postgresql/$PG_MAJOR/postgresql.conf.sample";  cp -v /usr/share/postgresql/postgresql.conf.sample.dpkg /usr/share/postgresql/postgresql.conf.sample;  ln -sv ../postgresql.conf.sample "/usr/share/postgresql/$PG_MAJOR/";  sed -ri "s!^#?(listen_addresses)\s*=\s*\S+.*!\1 = '*'!" /usr/share/postgresql/postgresql.conf.sample;  grep -F "listen_addresses = '*'" /usr/share/postgresql/postgresql.conf.sample
# RUN mkdir -p /var/run/postgresql && chown -R postgres:postgres /var/run/postgresql && chmod 2777 /var/run/postgresql
# ENV PGDATA=/var/lib/postgresql/data
# RUN mkdir -p "$PGDATA" && chown -R postgres:postgres "$PGDATA" && chmod 777 "$PGDATA"

# FROM debian:stretch-slim
# # COPY file:73785d4a64e88cd001941f2d0fb17c583e6d98ffda704b27106f5ef128737e5b in /usr/local/bin/ 
# RUN ln -s usr/local/bin/docker-entrypoint.sh / # backwards compat
# ENTRYPOINT ["docker-entrypoint.sh"]
# STOPSIGNAL SIGINT
# # EXPOSE 5432
# CMD ["postgres"]
FROM postgres:9.6.23-stretch as build
FROM scratch as postgres
COPY --from=build / /
ENV GOSU_VERSION=1.12
ENV LANG=en_US.utf8
ENV PG_MAJOR=9.6
ENV PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/lib/postgresql/9.6/bin
ENV PG_VERSION=9.6.23-1.pgdg90+1
ENV PGDATA=/var/lib/postgresql/data
ENTRYPOINT ["docker-entrypoint.sh"]
# EXPOSE 5432
CMD ["postgres"]