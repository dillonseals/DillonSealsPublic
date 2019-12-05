from djongo import models
from django.contrib.auth.models import User
from django.db.models.signals import post_save
#from django.dispatch import receiver

# Create your models here.

# clothing class
class Product(models.Model):
    image = models.ImageField(
        null='true',
        blank='true',
    )
    designer = models.CharField(max_length=100, null='true', blank='true')
    name = models.CharField(max_length=100, null='true', blank='true')
    price = models.PositiveSmallIntegerField(
        default=0
    )
    # colors
    BLUE = 'u'
    RED = 'r'
    YELLOW = 'y'
    PURPLE = 'p'
    ORANGE = 'o'
    GREEN = 'g'
    BROWN = 'n'
    WHITE = 'w'
    BLACK = 'b'
    SILVER = 's'
    COLOR_CHOICES = [
        (BLUE, 'Blue'),
        (RED, 'Red'),
        (YELLOW, 'Yellow'),
        (PURPLE, 'Purple'),
        (ORANGE, 'Orange'),
        (GREEN, 'Green'),
        (BROWN, 'Brown'),
        (WHITE, 'White'),
        (BLACK, 'Black'),
        (SILVER, 'Silver'),
    ]
    color = models.CharField(
        max_length=20,
        choices=COLOR_CHOICES,
        default=BLACK,
    )
    score = models.FloatField(
        null='true',
        blank='true',
    )
    # time/date
    # need some way of ordering
    # probably a post-330 todo

    def __str__(self):
        return self.name

# wish list
class Wish(models.Model):
    user = models.CharField(max_length=200, null='true', blank='true')
    stuff = models.CharField(max_length=200, null='true', blank='true')


# shopping cart
class Cart(models.Model):
    user = models.CharField(max_length=200, null='true', blank='true')
    stuff = models.CharField(max_length=200, null='true', blank='true')


# product reviews
# NOTE - user can leave multiple reviews for one product - fix in backend code
class Review(models.Model):
    reviewer = models.CharField(max_length=200, null='true', blank='true')
    product = models.CharField(max_length=200, null='true', blank='true')
    REVIEW_CHOICES = [
        ('1', 1),
        ('2', 2),
        ('3', 3),
        ('4', 4),
        ('5', 5),
    ]
    number = models.PositiveSmallIntegerField(
        choices=REVIEW_CHOICES,
        null='true',
        blank='true',
    )
    text = models.TextField(
        null='true',
        blank='true',
    )
    


# this was for a different db structure
""" @receiver(post_save, sender=User)
def createUserLists(sender, instance, created, **kwargs):
    if created:
        Lists.objects.create(user=instance)

@receiver(post_save, sender=User)
def saveUserList(sender, instance, **kwargs):
    instance.lists.save() """