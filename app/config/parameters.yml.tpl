# This file is auto-generated during the composer install
parameters:
    sylius.database.driver:   pdo_mysql
    sylius.database.host:     %(DatabaseHost)s
    sylius.database.port:     %(DatabasePort)s
    sylius.database.name:     %(DatabaseName)s
    sylius.database.user:     %(DatabaseUser)s
    sylius.database.password: %(DatabasePass)s
    sylius.mailer.transport: smtp
    sylius.mailer.host: 127.0.0.1
    sylius.mailer.user: null
    sylius.mailer.password: null
    sylius.locale: en
    sylius.secret: abc
    sylius.currency: CHF
    sylius.cache:
        type: file_system
    paypal.express_checkout.username: EDITME
    paypal.express_checkout.password: EDITME
    paypal.express_checkout.signature: EDITME
    paypal.express_checkout.sandbox: true
    stripe.secret_key: EDITME
    stripe.test_mode: true
    stripe.publishable_key: asdasd
    be2bill.identifier: EDITME
    be2bill.password: EDITME
    be2bill.sandbox: true
    sylius.oauth.amazon.clientid: '<amazon_client_id>'
    sylius.oauth.amazon.clientsecret: '<amazon_client_secret>'
    sylius.oauth.facebook.clientid: '<facebook_client_id>'
    sylius.oauth.facebook.clientsecret: '<facebook_client_secret>'
    sylius.oauth.google.clientid: '<google_client_id>'
    sylius.oauth.google.clientsecret: '<google_client_secret>'
    sylius.inventory.backorders_enabled: true
    sylius.inventory.tracking_enabled: true
    sylius.inventory.holding.duration: '15 minutes'
    sylius.promotion.item_based: false
    sylius.order.pending.duration: '3 hours'
    phpcr_backend:
        type: doctrinedbal
        connection: default
        caches:
            meta: doctrine_cache.providers.phpcr_meta
            nodes: doctrine_cache.providers.phpcr_nodes
    phpcr_workspace: default
    phpcr_user: admin
    phpcr_pass: admin
    base_url: '%(BaseUrl)s'
    google_analytics_ua: ''
    google_api_key: ''
    locale: en
    solr.dsn: %(SolrDsn)s
    
    sylius.order.allow_guest_order: false
    sylius.uploader.filesystem: sylius_image
    sylius.currency_importer.open_exchange_rates: 
                  CHF: 1.0
                  
    postpay.options:
          pspid: '%(PspId)s'
          userId: '%(UserId)s'
          cartUrl: '%(CartUrl)s'
          homeUrl: '%(BaseUrl)s'
          redirectUrl: '%(PostPayUrl)s'
          password: '%(PostPayPassword)s'
          
    email.to.exception: '%(EmailToException)s'
    email.from.exception: '%(EmailFromException)s'
    
    amazon_s3.key: '%(AmazonKey)s'
    amazon_s3.secret: '%(AmazonSecretKey)s'
    amazon_s3.bucket: '%(AmazonBucketName)s'