# Adobe Stock Image Admin UI Functional Tests

The Functional Test Module for **Magento AdobeStockImageAdminUI** module.

## Run

```bash
vendor/bin/mftf run:group AdobeStockIntegration
```

Or, if you want to run individual tests:

```bash
vendor/bin/mftf run:test <Test Name>
```

## Configuration

### `.env`

Add the following environment variables to `dev/tests/acceptance/.env`. They
describe details for the Magento instance you would like to run the tests
against:

- `MAGENTO_BASE_URL`
- `MAGENTO_BACKEND_NAME`
- `MAGENTO_ADMIN_USERNAME`
- `MAGENTO_ADMIN_PASSWORD`

i.e.:

    MAGENTO_BASE_URL=https://magento2.local/
    MAGENTO_BACKEND_NAME=admin_hgkq1l
    MAGENTO_ADMIN_USERNAME=admin
    MAGENTO_ADMIN_PASSWORD=admin123

More details about the configuration options available via `.env` can be found
on the [MFTF Configuration
documentation](https://devdocs.magento.com/mftf/docs/configuration.html).

### `.credentials`

Add the following environment variables to `dev/tests/acceptance/.credentials`.
These variables contain sensitive information related to your Adobe IMS and
Stock accounts and keys and thus MFTF treats them differently:

- `magento/ADOBE_STOCK_API_KEY`
- `magento/ADOBE_STOCK_PRIVATE_KEY`
- `magento/ADOBE_STOCK_USER_EMAIL`
- `magento/ADOBE_STOCK_USER_PASSWORD`

i.e.:

    magento/ADOBE_STOCK_API_KEY=12345
    magento/ADOBE_STOCK_PRIVATE_KEY=67890
    magento/ADOBE_STOCK_USER_EMAIL=shantanu@adobe.com
    magento/ADOBE_STOCK_USER_PASSWORD=password

More details about storing credentials in MFTF via `.credentials` can be found
on the [MFTF Credentials documentation](https://devdocs.magento.com/mftf/docs/credentials.html).
