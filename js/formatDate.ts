export default function formatDate(str: string) {
  if (!str) { return '' }
  const date = new Date(str)
  return date.toUTCString().slice(0, 16)
}