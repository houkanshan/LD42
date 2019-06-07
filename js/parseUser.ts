declare const Data: any

const now = Date.now()
const HOUR = 1000 * 60 * 60
const DAY = HOUR * 24

function parseDate(date) {
  return +(new Date(date))
}

export default function parseUser(user) {
  if (user.parsed) { return user }
  let endTime = now
  if (user.offline_time) {
    endTime = parseDate(user.offline_time)
  }
  const duration = endTime - parseDate(user.create_time)
  const days = Math.floor(duration / DAY)
  const hours = Math.floor(duration / HOUR)

  user.name = user.name.slice(0, 10)
  user.create_date = user.create_time.slice(0, 10)
  user.offline_date = user.offline_time && user.offline_time.slice(0, 10)
  user.update_date = user.update_time && user.update_time.slice(0, 10)
  user.story_date = user.story_time && user.story_time.slice(0, 10)

  user.level = +user.level + days
  user.score = +user.score + Math.floor(hours / 3)

  user.parsed = true
  return user
}

if (Data.users) {
  Data.users = Data.users.map(parseUser)
}
if (Data.me) {
  Data.me = parseUser(Data.me)
}