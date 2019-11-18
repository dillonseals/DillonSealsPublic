# Chatterer

Link - http://ec2-18-222-177-5.us-east-2.compute.amazonaws.com:3456/chatClient.html

We have created a hierarchy for each room:

  -  The Creator of a chatroon can dub other users as Overlords (admins) and Elders (mods). 
  -  The Creator may also revoke overlord and elder status.

  -  Overlords can make peasants into elders, but Overlords cannot appoint new Overlords.
  -  Overlords may also revoke elder privileges from a user.

  -  Elders cannot appoint or revoke privileges.

  -  All three tiers can kick and ban peasants (normal users). 
  -  Users with privileges cannot kick/ban other users at their level or higher - Eg. elders cannot kick overlords, and overlords cannot kick the creator.
