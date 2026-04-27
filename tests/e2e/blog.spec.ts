import { test, expect } from '@playwright/test';

test.describe('Home page', () => {
  test('loads successfully', async ({ page }) => {
    await page.goto('/');
    await expect(page).toHaveURL('/');
    await expect(page.locator('body')).toBeVisible();
  });
});

test.describe('Category page', () => {
  test('returns a valid response', async ({ page }) => {
    const response = await page.goto('/category/1');
    expect(response?.status()).toBeLessThan(500);
  });
});

test.describe('Post page', () => {
  test('returns a valid response', async ({ page }) => {
    const response = await page.goto('/post/1');
    expect(response?.status()).toBeLessThan(500);
  });
});
