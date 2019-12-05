from django.contrib import admin
from .models import Product
from .models import Wish
from .models import Cart
from .models import Review

# Register your models here.

admin.site.register(Product)
admin.site.register(Wish)
admin.site.register(Cart)
admin.site.register(Review)