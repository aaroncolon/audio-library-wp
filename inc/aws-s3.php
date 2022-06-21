<?php
require get_stylesheet_directory() .'/inc/aws-sdk-3/aws.phar';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

function ml_get_s3_presigned_request($filename = '') {
  if (! defined('AS3CF_SETTINGS')) { return null; }

  $awsCreds = unserialize(AS3CF_SETTINGS);
  $awsKey = $awsCreds['access-key-id'];
  $awsSecret = $awsCreds['secret-access-key'];

  $s3Client = new Aws\S3\S3Client([
    // 'profile' => 'default',
    'region' => 'us-east-1',
    'version' => 'latest',
    'credentials' => [
      'key'    => $awsKey,
      'secret' => $awsSecret,
    ]
  ]);
  
  $cmd = $s3Client->getCommand('GetObject', [
    'Bucket' => '',
    'Key' => ''
  ]);
  
  $request = $s3Client->createPresignedRequest($cmd, '+5 minutes');

  return $presignedUrl = (string) $request->getUri();
}
