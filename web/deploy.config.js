/* eslint-disable @typescript-eslint/no-require-imports */
module.exports = {
  server: {
    host: '35.80.110.71',
    username: 'ubuntu',
    privateKeyPath: require('os').homedir() + '/.ssh/ps4_new',
  },
  paths: {
    basePath: '/var/www/webhook-relay-web',
  },
  pm2Process: 'webhook-relay-web',
  filesToTransfer: ['.next', 'public', 'package.json', 'package-lock.json', 'next.config.ts'],
  releasesToKeep: 3,
};
