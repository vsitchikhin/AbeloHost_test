import { test, expect } from '@playwright/test';

test.describe('Home page', () => {
  test('renders the blog home page', async ({ page }) => {
    const response = await page.goto('/');

    expect(response?.status()).toBe(200);
    await expect(page.getByTestId('home-page')).toBeVisible();
    await expect(page.locator('body')).toBeVisible();
  });
});

test.describe('Category page', () => {
  test('renders category or not found template without server errors', async ({ page }) => {
    const response = await page.goto('/category/1');

    expect(response?.status()).toBeLessThan(500);
    await expect(page.locator('body')).toBeVisible();
    await expect(page.getByTestId(/category-page|not-found-page/)).toBeVisible();
  });

  test('renders not found template for missing category', async ({ page }) => {
    const response = await page.goto('/category/missing-category');

    expect(response?.status()).toBe(404);
    await expect(page.getByTestId('not-found-page')).toBeVisible();
  });
});

test.describe('Post page', () => {
  test('renders post or not found template without server errors', async ({ page }) => {
    const response = await page.goto('/post/1');

    expect(response?.status()).toBeLessThan(500);
    await expect(page.locator('body')).toBeVisible();
    await expect(page.getByTestId(/post-page|not-found-page/)).toBeVisible();
  });

  test('renders not found template for missing post', async ({ page }) => {
    const response = await page.goto('/post/missing-post');

    expect(response?.status()).toBe(404);
    await expect(page.getByTestId('not-found-page')).toBeVisible();
  });
});
