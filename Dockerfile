FROM oven/bun:1.1.7-debian

ENV NODE_ENV="production"

ENV DATABASE_URL="postgresql://postgres:password@db:5432/zuteilungsalgorithmus"
ENV NEXTAUTH_SECRET=""
ENV NEXTAUTH_URL="http://localhost:3000"

# For login of the user admin
ENV ADMIN_USER="admin"
ENV ADMIN_PASSWORD=""

# LDAP login
ENV LDAP_URI="ldaps://10.16.1.1:636"
ENV LDAP_BASE_DN="ou=accounts,dc=schule,dc=local"
ENV LDAP_REALM="My Realm"

RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get -y install --no-install-recommends python3 python3-pip \
    && bun install --frozen-lockfile --production


EXPOSE 3000/tcp

ENTRYPOINT [ "sh", "-c", "bun run build && bun run start" ]
