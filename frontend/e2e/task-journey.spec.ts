import { expect, test } from '@playwright/test'

test.use({ colorScheme: 'dark' })

test('user completes the main task journey', async ({ page }) => {
  const originalTitle = `E2E task ${Date.now()}`
  const editedTitle = `${originalTitle} updated`

  const iconResponse = await page.request.get('/_nuxt_icon/lucide.json?icons=loader-circle')
  expect(iconResponse.ok()).toBe(true)

  await page.goto('/login')
  await expect(page.locator('html')).not.toHaveClass(/dark/)
  await expect(page.locator('html')).toHaveAttribute('data-hydrated', 'true')
  await page.getByLabel('Email').fill('user@example.com')
  await page.getByLabel('Password').fill('Password123!')
  await page.getByRole('button', { name: 'Log in' }).click()
  await expect(page).toHaveURL(/\/tasks/)

  await page.getByRole('button', { name: 'Create task' }).click()
  const createDialog = page.getByRole('dialog')
  await createDialog.getByLabel('Title').fill(originalTitle)
  await createDialog.getByLabel('Description').fill('Created by the Playwright journey.')
  await createDialog.getByRole('button', { name: 'Create task' }).click()
  await expect(page.getByText(originalTitle, { exact: true })).toBeVisible()

  await page.getByLabel('Search tasks').fill(originalTitle)
  await expect(page).toHaveURL(/search=E2E/)
  const row = page.getByRole('row').filter({ hasText: originalTitle })
  await expect(row).toBeVisible()

  await row.getByRole('button', { name: 'Edit' }).click()
  const editDialog = page.getByRole('dialog')
  await editDialog.getByLabel('Title').fill(editedTitle)
  await editDialog.getByRole('button', { name: 'Save changes' }).click()
  await expect(page.getByText(editedTitle, { exact: true })).toBeVisible()

  const updatedRow = page.getByRole('row').filter({ hasText: editedTitle })
  await updatedRow.getByLabel(`Change status for ${editedTitle}`).click()
  await page.getByRole('option', { name: 'Completed' }).click()
  await expect(updatedRow.getByLabel(`Change status for ${editedTitle}`)).toContainText('Completed')

  await updatedRow.getByRole('button', { name: 'Delete' }).click()
  const deleteDialog = page.getByRole('dialog')
  await deleteDialog.getByRole('button', { name: 'Delete task' }).click()
  await expect(page.getByText(editedTitle, { exact: true })).toHaveCount(0)

  await page.getByRole('button', { name: 'Log out' }).click()
  await expect(page).toHaveURL(/\/login/)

  await page.goto('/tasks')
  await expect(page).toHaveURL(/\/login/)
})
