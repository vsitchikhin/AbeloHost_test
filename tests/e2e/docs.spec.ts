import { test, expect } from '@playwright/test';

test.describe('API documentation', () => {
  test('serves Swagger UI', async ({ page }) => {
    const response = await page.goto('/api/docs');

    expect(response?.status()).toBe(200);
    await expect(page.locator('#swagger-ui')).toBeVisible();
  });

  test('serves OpenAPI specification', async ({ request }) => {
    const response = await request.get('/api/docs/openapi.yaml');

    expect(response.status()).toBe(200);
    expect(response.headers()['content-type']).toContain('application/yaml');
    expect(await response.text()).toContain('openapi: 3.0.3');
  });
});
