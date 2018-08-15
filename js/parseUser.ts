const now = Date.now()
const HOUR = 1000 * 60 * 60
const DAY = HOUR * 24

function parseDate(date) {
  return +(new Date(date))
}

export default function(user) {
  if (user.parsed) { return user }
  let endTime = now
  if (user.offline_time) {
    endTime = parseDate(user.offline_time)
  }
  const duration = endTime - parseDate(user.create_time)
  const days = Math.floor(duration / DAY)
  const hours = Math.floor(duration / HOUR)

  user.level = +user.level + days
  user.score = +user.score + Math.floor(hours / 2)

  user.parsed = true
  return user
}