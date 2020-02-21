SELECT starttime,
       targetid,
       views,
       clicks,
       (clicks / views) * 100 as crt,
       users,
       users_who_clicked
FROM (
         SELECT starttime,
                targetid,
                (SELECT count(*) FROM pages_log as v WHERE eventname = 'view' AND l.targetid = v.targetid)            as views,
                (SELECT count(*)
                 FROM pages_log as c
                 WHERE eventname = 'click'
                   AND l.targetid = c.targetid)                                                                       as clicks,
                (SELECT COUNT(DISTINCT uid)
                 FROM pages_log as v
                 WHERE eventname = 'view'
                   AND l.targetid = v.targetid)                                                                       as users,
                (SELECT COUNT(DISTINCT uid)
                 FROM pages_log as v
                 WHERE eventname = 'click'
                   AND l.targetid = v.targetid)                                                                       as users_who_clicked
         from pages_log as l
     ) as t
GROUP BY targetid, starttime
ORDER BY starttime