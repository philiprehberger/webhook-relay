/* eslint-disable @typescript-eslint/no-require-imports */
module.exports = {
  server: {
    host: '44.254.213.202',
    username: 'ubuntu',
    privateKeyPath: require('os').homedir() + '/.ssh/scopeforged_rebuild',
  },
  paths: {
    basePath: '/var/www/webhook-relay-web',
  },
  pm2Process: 'webhook-relay-web',
  filesToTransfer: ['.next', 'public', 'package.json', 'package-lock.json', 'next.config.ts'],
  releasesToKeep: 3,
};
