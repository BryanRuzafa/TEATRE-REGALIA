module.exports = {
  apps: [
    {
      name: 'TR-Backend-Laravel',
      cwd: './backend',
      script: 'artisan',
      args: 'serve --host=0.0.0.0 --port=8000',
      interpreter: 'php',
      instances: 1,
      autorestart: true,
      watch: false
    },
    {
      name: 'TR-Frontend-Nuxt',
      cwd: './frontend',
      script: '.output/server/index.mjs',
      env: {
        PORT: 3002,
        NITRO_HOST: '127.0.0.1',
        HOST: '127.0.0.1',
        NODE_ENV: 'production'
      }
    },
    {
      name: 'TR-Realtime-Sockets',
      cwd: './realtime',
      script: 'index.js',
      instances: 1,
      autorestart: true,
      watch: false
    }
  ]
}
