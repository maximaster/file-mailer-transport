# File Mailer Transport

Do you want to save your symfony/mailer emails as files (e.g. `.EML`)?

You've just found the right package!

## Usage

**services.yaml**

```yaml
    Maximaster\FileMailerTransport\TransportFactory:
        arguments:
            $projectDir: '%kernel.project_dir%'

    mailer.default_transport:
        class: Symfony\Component\Mailer\Transport\TransportInterface
        factory: '@Maximaster\FileMailerTransport\TransportFactory'
        arguments:
            $dsn: '%env(MAILER_DSN)%'
```

**.env**

```dotenv
MAILER_DSN=file:///upload/emails/%Y/%m/%d/%T_@hash.eml
```

## Options

* new_directory_mode - with which rights new directory will be created
* hash_algo - controls how `@hash` replacement is calculated
* path_renderer - callback which is supposed to render a real file path from a path template

