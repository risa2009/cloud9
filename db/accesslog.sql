SELECT
  DATE(access_datetime)    as date,
  COUNT(user_id)           as pv,
  COUNT(DISTINCT(user_id)) as uu
FROM
  access
GROUP BY
  DATE(access_datetime)
