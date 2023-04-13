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

```env
MAGENTO_BASE_URL=https://magento2.local/
MAGENTO_BACKEND_NAME=admin_hgkq1l
MAGENTO_ADMIN_USERNAME=admin
MAGENTO_ADMIN_PASSWORD=admin123
```

More details about the configuration options available via `.env` can be found
on the [MFTF Configuration
documentation](https://developer.adobe.com/commerce/testing/functional-testing-framework/configuration/).

### `.credentials`

Add the following environment variables to `dev/tests/acceptance/.credentials`.
These variables contain sensitive information related to your Adobe IMS and
Stock accounts and keys and thus MFTF treats them differently:

- `magento/adobe_stock_api_key`
- `magento/adobe_stock_private_key`
- `magento/adobe_stock_user_email`
- `magento/adobe_stock_user_password`

i.e.:

```env
magento/adobe_stock_api_key=12345
magento/adobe_stock_private_key=67890
magento/adobe_stock_user_email=shantanu@adobe.com
magento/adobe_stock_user_password=password
```

More details about storing credentials in MFTF via `.credentials` can be found
on the [MFTF Credentials documentation](https://developer.adobe.com/commerce/testing/functional-testing-framework/credentials/).
