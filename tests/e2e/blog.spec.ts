import { test, expect } from '@playwright/test';

test.describe('Home page', () => {
  test('renders categories with latest posts', async ({ page }) => {
    const response = await page.goto('/');

    expect(response?.status()).toBe(200);
    await expect(page.getByTestId('home-page')).toBeVisible();
    await expect(page.getByRole('heading', { exact: true, name: 'Technology' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Newest Technology Article' })).toBeVisible();
    await expect(page.locator('a[href="/category/technology"]', { hasText: 'All articles' })).toBeVisible();
  });
});

test.describe('Category page', () => {
  test('renders category by slug', async ({ page }) => {
    const response = await page.goto('/category/technology');

    expect(response?.status()).toBe(200);
    await expect(page.getByTestId('category-page')).toBeVisible();
    await expect(page.getByTestId('category-page').getByRole('heading', { name: 'Technology' })).toBeVisible();
    await expect(page.getByTestId('post-card')).toHaveCount(9);
  });

  test('sorts category posts by views', async ({ page }) => {
    const response = await page.goto('/category/technology?sort=views&dir=desc');

    expect(response?.status()).toBe(200);
    await expect(page.getByTestId('post-card').first()).toHaveAttribute(
      'data-post-slug',
      'most-viewed-technology-article',
    );
  });

  test('paginates category posts', async ({ page }) => {
    const response = await page.goto('/category/technology?page=2');

    expect(response?.status()).toBe(200);
    await expect(page.getByTestId('post-card')).toHaveCount(3);
    await expect(page.getByRole('heading', { name: 'First Post' })).toBeVisible();
  });

  test('renders not found template for missing category', async ({ page }) => {
    const response = await page.goto('/category/missing-category');

    expect(response?.status()).toBe(404);
    await expect(page.getByTestId('not-found-page')).toBeVisible();
  });
});

test.describe('Post page', () => {
  test('renders post by slug with similar posts', async ({ page }) => {
    const response = await page.goto('/post/first-post');

    expect(response?.status()).toBe(200);
    await expect(page.getByTestId('post-page')).toBeVisible();
    await expect(page.getByRole('heading', { name: 'First Post' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Similar articles' })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Newest Technology Article' })).toBeVisible();
  });

  test('increments post views', async ({ page }) => {
    await page.goto('/post/views-counter-post');
    await expect(page.getByTestId('post-page').getByText('10 views')).toBeVisible();

    await page.goto('/post/views-counter-post');
    await expect(page.getByTestId('post-page').getByText('11 views')).toBeVisible();
  });

  test('renders not found template for missing post', async ({ page }) => {
    const response = await page.goto('/post/missing-post');

    expect(response?.status()).toBe(404);
    await expect(page.getByTestId('not-found-page')).toBeVisible();
  });
});
