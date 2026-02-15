export function groupEntriesByDate(entries = []) {
  const now = new Date()
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
  const yesterday = new Date(today)
  yesterday.setDate(yesterday.getDate() - 1)

  const weekAgo = new Date(today)
  weekAgo.setDate(weekAgo.getDate() - 7)

  const monthAgo = new Date(today)
  monthAgo.setMonth(monthAgo.getMonth() - 1)

  const groups = {
    Today: [],
    Yesterday: [],
    'This Week': [],
    'This Month': [],
    Older: [],
  }

  for (const entry of entries) {
    const createdAt = new Date(entry.created_at)

    if (createdAt >= today) groups.Today.push(entry)
    else if (createdAt >= yesterday) groups.Yesterday.push(entry)
    else if (createdAt >= weekAgo) groups['This Week'].push(entry)
    else if (createdAt >= monthAgo) groups['This Month'].push(entry)
    else groups.Older.push(entry)
  }

  return Object.entries(groups).filter(([, items]) => items.length > 0)
}
