#!/bin/bash
set -e

# ca must be different than the domain
DOMAIN=spaceapi.net
FILE=$DOMAIN.2014
DOMAIN_VALIDITY=729
CA=ca.$DOMAIN
CAFILE=$CA.2014
CA_VALIDITY=730

COUNTRY=DE
STATE=Germany
LOCATION="South-South-North"
ORGANIZATION="SpaceAPI Organization"
ORGANIZATIONAL_UNIT="asdf"
EMAIL=no-reply@$DOMAIN

read -p "Should I create a new CA? (y/N) "
echo
if [[ $REPLY =~ ^[Yy][a-zA-Z]*$ ]]
then
    # CA-Config erstellen
	cat > $CA.cnf <<-EOF
		[req]
		distinguished_name = distinguished_name
		prompt = no

		[distinguished_name]
		C  = $COUNTRY
		ST = $STATE
		L  = $LOCATION
		O  = $ORGANIZATION
		OU = $ORGANIZATIONAL_UNIT
		CN = $CA
		emailAddress = $EMAIL
	EOF
	echo "Creating CA"
	openssl genrsa -aes256 -out $CAFILE.key 4096
	openssl req -new -x509 -config $CA.cnf -days $CA_VALIDITY -sha256 -key $CAFILE.key -out $CAFILE.crt
fi

# Domain cert config
cat > $DOMAIN.cnf <<-EOF
	[req]
	distinguished_name = distinguished_name
	req_extensions = extensions
	prompt = no

	[distinguished_name]
	C  = $COUNTRY
	ST = $STATE
	L  = $LOCATION
	O  = $ORGANIZATION
	OU = $ORGANIZATIONAL_UNIT
	CN = $DOMAIN
	emailAddress = $EMAIL

	[extensions]
	basicConstraints = CA:FALSE
	subjectAltName = @alt_names

	[alt_names]
	DNS.1 = $DOMAIN
	DNS.2 = *.$DOMAIN
EOF

echo "Creating and signing domain key and certificate"
openssl genrsa -out $FILE.key 4096
openssl req -new -config $DOMAIN.cnf -key $FILE.key -out $FILE.csr
openssl x509 -req -in $FILE.csr -out $FILE.crt -sha256 -CA $CAFILE.crt -CAkey $CAFILE.key -CAcreateserial -days $DOMAIN_VALIDITY -extensions extensions -extfile $DOMAIN.cnf
echo
echo "Fingerprint, subject and dates of your domain certificate:"
openssl x509 -subject -dates -fingerprint -in $FILE.crt -out /dev/null