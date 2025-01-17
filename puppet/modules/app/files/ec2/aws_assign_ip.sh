#!/bin/bash

AWS_ACCESS_KEY="AKIAIG4GFINOWTIQHL7A"
AWS_SECRET_KEY="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"

export EC2_INSTANCE_ID=`curl -s http://169.254.169.254/latest/meta-data/instance-id`
export EC2_HOME=/usr/local/ec2-api-tools
export PATH=$PATH:$EC2_HOME/bin
export JAVA_BIN=`readlink /etc/alternatives/java`
export JAVA_HOME="${JAVA_BIN/\/bin\/java/}"


EC2_ASSOC_ADDRESS="$EC2_HOME/bin/ec2-associate-address"
EC2_DESC_TAGS="$EC2_HOME/bin/ec2-describe-tags"
EC2_INSTANCE_ID=`curl -s http://169.254.169.254/latest/meta-data/instance-id`
ELASTIC_IP_TAG=`$EC2_DESC_TAGS --aws-access-key $AWS_ACCESS_KEY --aws-secret-key $AWS_SECRET_KEY | grep $EC2_INSTANCE_ID | grep elastic_ip`
ELASTIC_IP=`echo $ELASTIC_IP_TAG | rev | cut -d " " -f1 | rev`

echo "Assigning this instance '$EC2_INSTANCE_ID' the elastic IP '$ELASTIC_IP' based on the 'elastic_ip' tag for instance"
$EC2_ASSOC_ADDRESS --aws-access-key $AWS_ACCESS_KEY --aws-secret-key $AWS_SECRET_KEY $ELASTIC_IP -i $EC2_INSTANCE_ID
