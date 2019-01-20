export default function formatDate(str: string, hasTime = false) {
  if (!str) { return '' }
  const date = new Date(str)
  return date.toUTCString().slice(0, hasTime ? 25 : 16)
}